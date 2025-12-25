<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Http, DB, Log};
use Illuminate\Support\Str;
use Carbon\Carbon;

class WhatsaapBotController extends Controller
{
    private $chatModel = 'gpt-4o-mini';
    private $embeddingModel = 'text-embedding-3-small';

    public function handleWebhook(Request $request)
{
    $startTime = microtime(true); // Yanıt süresi ölçümü için başlangıç

    try {
        $companyId = $request->input('company_id');
        $phone     = $this->formatPhoneNumber($request->input('phone'));
        $userMsg   = trim((string) $request->input('message'));
        $userName  = $request->input('name', 'Müşteri');

        if (!$companyId || !$phone || !$userMsg) {
            return response()->json(['reply' => 'Geçersiz veri.'], 200);
        }

        // 1. Şirket ve API Key Kontrolü
        $company = DB::table('companies')->where('id', $companyId)->first();
        if (!$company || $company->status !== 'Aktif') {
            return response()->json(['reply' => 'Şirket pasif.'], 200);
        }

        $apiKey = ($company->use_own_key && $company->openai_api_key)
            ? $company->openai_api_key
            : config('services.openai.key');

        // 2. Kişi ve Oturum Yönetimi
        $contactId = $this->getOrCreateContact($companyId, $phone, $userName);
        $sessionId = $this->resolveSession($contactId);

        // Gelen mesajı kaydet
        DB::table('whatsapp_messages')->insert([
            'company_id' => $companyId,
            'contact_id' => $contactId,
            'session_id' => $sessionId,
            'type'       => 'incoming',
            'message'    => $userMsg,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 3. RAG (Embedding & Search)
        $userVector = $this->generateEmbedding($userMsg, $apiKey);
        $contextContent = $this->searchKnowledgeBase($companyId, $userVector);

        // 4. OpenAI Chat
        $history = $this->getChatHistory($contactId);

        $systemPrompt = "Sen {$company->name} WhatsApp AI asistanısın. 
        Müşteriye doğal ve yardımcı bir dille cevap ver.
        
        ZORUNLU JSON FORMAT:
        {
          \"reply\": \"string\",
          \"sentiment\": \"Pozitif | Negatif | Nötr\",
          \"intent\": \"Satış | Destek | Bilgi | Şikayet | Diğer\",
          \"csat\": 1-5,
          \"keywords\": [\"kelime1\", \"kelime2\"],
          \"is_fallback\": false,
          \"is_handover\": false
        }
        
        Kurallar:
        - CONTEXT dışında bilgi kullanma.
        - Müşteri insanla görüşmek isterse is_handover = true yap.
        - Emin değilsen is_fallback = true yap.";

        $chatRes = Http::withToken($apiKey)->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $this->chatModel,
            'messages' => array_merge(
                [['role' => 'system', 'content' => $systemPrompt]],
                $history,
                [['role' => 'user', 'content' => "CONTEXT:\n{$contextContent}\n\nQUESTION:\n{$userMsg}"]]
            ),
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.2
        ]);

        if (!$chatRes->successful()) throw new \Exception("OpenAI Hatası: " . $chatRes->body());

        // 5. JSON Parse
        $aiRaw = $chatRes->json()['choices'][0]['message']['content'] ?? null;
        $aiData = json_decode($aiRaw, true);
        $usage = $chatRes->json()['usage'] ?? [];

        // 6. Yanıt Süresini Hesapla
        $responseTime = round((microtime(true) - $startTime), 2);

        // 7. Yanıtı Kaydet ve Oturumu Güncelle
        $this->saveOutgoingMessage($companyId, $contactId, $sessionId, $aiData, $usage, $responseTime);

        return response()->json(['reply' => $aiData['reply']], 200);

    } catch (\Throwable $e) {
        Log::error("Bot Hatası: " . $e->getMessage());
        return response()->json(['reply' => 'Şu an teknik bir sorun yaşıyorum.'], 200);
    }
}

    // --- YARDIMCI METODLAR ---
    private function formatPhoneNumber($phone) { return preg_replace('/[^0-9]/', '', (string) $phone); }

    private function getOrCreateContact($companyId, $phone, $name) {
        $id = DB::table('whatsapp_contacts')->where(['company_id' => $companyId, 'phone' => $phone])->value('id');
        if ($id) return $id;
        return DB::table('whatsapp_contacts')->insertGetId(['company_id' => $companyId, 'phone' => $phone, 'name' => $name, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function resolveSession($contactId) {
        $last = DB::table('whatsapp_messages')->where('contact_id', $contactId)->latest()->first();
        if ($last && Carbon::parse($last->created_at)->diffInMinutes(now()) < 30) return $last->session_id;
        return (string) Str::uuid();
    }

    private function generateEmbedding($text, $key) {
        $res = Http::withToken($key)->post('https://api.openai.com/v1/embeddings', ['model' => $this->embeddingModel, 'input' => $text]);
        return $res->json()['data'][0]['embedding'];
    }
    private function searchKnowledgeBase($companyId, $userVector) {
    $knowledges = DB::table('knowledge_bases')->where('company_id', $companyId)->get();
    $matches = [];

    foreach ($knowledges as $kb) {
        $dbVector = json_decode($kb->embedding, true);
        if (!$dbVector) continue;

        $score = $this->cosineSimilarity($userVector, $dbVector);
        
        // Log ile skoru izlemeye devam edelim
        Log::info("KB Skor Kontrol: Content ID: {$kb->id}, Score: {$score}");

        // EŞİĞİ 0.35'E DÜŞÜRÜYORUZ
        // 0.46 olan adres bilginiz artık bu süzgece takılmayacak ve geçecek.
        if ($score > 0.35) { 
            $matches[] = ['score' => $score, 'text' => $kb->content];
        }
    }

    if (empty($matches)) {
        Log::warning("Hiçbir bilgi kaynağı ile eşleşme sağlanamadı (Eşik altı).");
        return ""; // Boş gönderirsek OpenAI kendi bilgisiyle (varsa) cevap vermeye çalışır veya reddeder.
    }

    // Skorlara göre sırala (En yüksek üstte)
    usort($matches, fn($a, $b) => $b['score'] <=> $a['score']);
    
    // En iyi 3 sonucu birleştir
    return collect($matches)->take(3)->pluck('text')->implode("\n---\n");
}
    private function getChatHistory($contactId) {
        return DB::table('whatsapp_messages')->where('contact_id', $contactId)->latest()->take(6)->get()->reverse()->map(fn($m) => [
            'role' => $m->type === 'incoming' ? 'user' : 'assistant',
            'content' => $m->message
        ])->values()->toArray();
    }

    private function saveOutgoingMessage($companyId, $contactId, $sessionId, $aiData, $usage, $responseTime)
{
    // 1. Mesajı whatsapp_messages tablosuna ekle
    DB::table('whatsapp_messages')->insert([
        'company_id'     => $companyId,
        'contact_id'     => $contactId,
        'session_id'     => $sessionId,
        'type'           => 'outgoing',
        'message'        => $aiData['reply'] ?? '',
        'sentiment'      => $aiData['sentiment'] ?? 'Nötr',
        'intent'         => $aiData['intent'] ?? null,
        'keywords'       => isset($aiData['keywords']) ? json_encode($aiData['keywords']) : null,
        'predicted_csat' => $aiData['csat'] ?? null,
        'is_fallback'    => $aiData['is_fallback'] ?? false,
        'is_handover'    => $aiData['is_handover'] ?? false,
        'token_usage'    => $usage['total_tokens'] ?? 0,
        'created_at'     => now(),
        'updated_at'     => now(),
    ]);

    // 2. conversation_sessions tablosunu güncelle (Aggregated Data)
    $stats = DB::table('whatsapp_messages')
        ->where('session_id', $sessionId)
        ->select(
            DB::raw('COUNT(*) as total'),
            DB::raw('AVG(predicted_csat) as avg_happiness')
        )
        ->first();

    DB::table('conversation_sessions')->updateOrInsert(
        ['session_id' => $sessionId],
        [
            'company_id'         => $companyId,
            'contact_id'         => $contactId,
            'message_count'      => $stats->total,
            'avg_csat'           => round($stats->avg_happiness, 2),
            // Mevcut ortalama hızı ve yeni hızı harmanlayarak kaydet
            'avg_response_time'  => DB::raw("COALESCE((avg_response_time + $responseTime) / 2, $responseTime)"),
            'updated_at'         => now()
        ]
    );
}

    private function cosineSimilarity($a, $b) {
        $dot = 0; $n1 = 0; $n2 = 0;
        foreach ($a as $i => $v) {
            $dot += $v * ($b[$i] ?? 0);
            $n1 += $v ** 2; $n2 += ($b[$i] ?? 0) ** 2;
        }
        return ($n1 && $n2) ? $dot / (sqrt($n1) * sqrt($n2)) : 0;
    }
}