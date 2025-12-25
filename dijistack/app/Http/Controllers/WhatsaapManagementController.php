<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Helpers\Permission;

class WhatsaapManagementController extends Controller
{
    // Whatsaap Bağlantı Sayfası
    public function connection($domain)
    {
        if (!can("whatsapp-management/connection", "read")) {
            return view('no-authority');
        }
        $company = auth()->user()->company_id;
        return view("whatsaap-management.connection", compact("company"));
    }
    // Ürün ve Hizmetler Liste Sayfası
    public function index($domain)
    {
        if (!can("whatsapp-management/services", "read")) {
          return view('no-authority');
        }elseif (!can("whatsapp-management/services", "update")) {
             return view('no-authority');
        }
        return view("whatsaap-management.services.list");
    }
    // Ürün ve Hizmetleri Listele
    public function fetchServices(Request $request)
    {
        if (!can("whatsapp-management/services", "read")) {
         return view('no-authority');
        }
        $companyId = auth()->user()->company_id;

        $query = DB::table("knowledge_bases")
            ->where("company_id", $companyId)
            ->select(["id", "content", "title", "created_at"])
            ->orderBy("id", "desc");

        return datatables()
            ->of($query)
            ->editColumn("title", function ($row) {
                return Str::limit(strip_tags($row->title), 120);
            })
            ->editColumn("content", function ($row) {
                return Str::limit(strip_tags($row->content), 120);
            })
            ->editColumn("created_at", function ($row) {
                return Carbon::parse($row->created_at)->format("d.m.Y H:i");
            })
            ->addColumn("actions", function ($row) {
                return '
               <div class="btn-group">
            <button class="btn btn-sm btn-success edit-btn me-2" 
                    data-id="' .
                    $row->id .
                    '" 
                    data-title="' .
                    $row->title .
                    '" 
                    data-content="' .
                    htmlspecialchars($row->content) .
                    '">
                <i class="fa fa-edit"></i> Düzenle
            </button>
            <button class="btn btn-sm btn-danger delete-btn" data-id="' .
                    $row->id .
                    '">
                <i class="fa fa-trash"></i> Sil
            </button>
        </div>';
            })
            ->rawColumns(["actions"])
            ->make(true);
    }
    // Manuel Ürün ve Hizmet Ekleme Sayfası
    public function createService()
    {
        if (!can("whatsapp-management/services/create", "create")) {
            return view('no-authority');
        } elseif (!can("whatsapp-management/services/create", "read")) {
           return view('no-authority');
        }
        return view("whatsaap-management.services.manuel-create");
    }
    public function storeService(Request $request)
    {
        if (!can("whatsapp-management/services", "create")) {
            abort(403, "Bu sayfaya erişim yetkiniz yok.");
        }
        $request->validate([
            "title" => "required|string|max:255",
            "content" => "required|string",
        ]);

        $companyId = auth()->user()->company_id;
        $company = DB::table("companies")
            ->where("id", $companyId)
            ->first();

        // API Key Belirleme
        $apiKey =
            $company->use_own_key && $company->openai_api_key
                ? $company->openai_api_key
                : env("OPENAI_API_KEY");

        try {
            // 1. OpenAI Vektör İsteği
            $response = Http::withToken($apiKey)->post(
                "https://api.openai.com/v1/embeddings",
                [
                    "model" => "text-embedding-3-small",
                    "input" => "Başlık: {$request->title}\nİçerik: {$request->content}",
                ]
            );

            // API Yanıt Vermezse veya Hata Dönerse
            if (!$response->successful()) {
                // Loglama: Hangi şirket, hangi hata kodu ve ham hata mesajı
                Log::error("OpenAI Embedding API Hatası", [
                    "company_id" => $companyId,
                    "status" => $response->status(),
                    "response" => $response->json(),
                    "title" => $request->title,
                ]);

                throw new \Exception(
                    "Vektör oluşturulamadı. Detaylar log dosyasına kaydedildi."
                );
            }

            $embedding = $response->json()["data"][0]["embedding"];

            // 2. Veritabanına Kaydet
            DB::table("knowledge_bases")->insert([
                "company_id" => $companyId,
                "title" => $request->title,
                "content" => "Hizmet: {$request->title}\nDetay: {$request->content}",
                "embedding" => json_encode($embedding),
                "created_at" => now(),
                "updated_at" => now(),
            ]);

            return response()->json(["status" => "success"]);
        } catch (\Exception $e) {
            Log::critical("Manuel Hizmet Ekleme İşleminde Kritik Hata", [
                "company_id" => $companyId,
                "error_message" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
            ]);
            return response()->json(
                [
                    "status" => "error",
                    "message" =>
                        "İşlem sırasında bir hata oluştu: " . $e->getMessage(),
                ],
                500
            );
        }
    }
    // PDF ile Ürün ve Hizmet Ekleme Sayfası
    public function createServiceViaPDF()
    {
        if (!can("whatsapp-management/services/create-pdf", "create")) {
            abort(403, "Bu sayfaya erişim yetkiniz yok.");
        } elseif (!can("whatsapp-management/services/create-pdf", "read")) {
             return view('no-authority');
        }
        return view("whatsaap-management.services.pdf-create");
    }
    // PDF ile Ürün ve Hizmet Ekleme İşlemi
    public function storePDF(Request $request)
    {
        if (!can("whatsapp-management/services/create-pdf", "create")) {
            abort(403, "Bu sayfaya erişim yetkiniz yok.");
        }
        $request->validate([
            "pdf_file" => "required|mimes:pdf|max:10240", // 10MB idealdir
        ]);

        $companyId = auth()->user()->company_id;
        $company = DB::table("companies")
            ->where("id", $companyId)
            ->first();
        $apiKey = $company->openai_api_key;

        if (!$apiKey) {
            return response()->json(["error" => "API Key bulunamadı."], 422);
        }

        try {
            // 1. PDF Metnini Ayıkla
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile(
                $request->file("pdf_file")->getPathname()
            );
            $text = $pdf->getText();

            // Temizlik: Gereksiz boşlukları ve satır başlarını temizle
            $text = preg_replace("/\s+/", " ", $text);
            $text = trim($text);

            if (strlen($text) < 10) {
                return response()->json(
                    [
                        "error" =>
                            "PDF içeriği boş veya okunabilir metin bulunamadı.",
                    ],
                    422
                );
            }

            // 2. Tek Seferde OpenAI Embedding Al
            $response = Http::withToken($apiKey)->post(
                "https://api.openai.com/v1/embeddings",
                [
                    "model" => "text-embedding-3-small",
                    "input" => $text,
                ]
            );

            if (!$response->successful()) {
                Log::error("PDF Embedding Hatası", [
                    "response" => $response->json(),
                ]);
                return response()->json(
                    [
                        "error" =>
                            "OpenAI veriyi işleyemedi (Dosya çok uzun olabilir).",
                    ],
                    500
                );
            }

            $embedding = $response->json()["data"][0]["embedding"];

            DB::table("knowledge_bases")->insert([
                "company_id" => $companyId,
                "content" =>
                    "Dosya Adı: " .
                    $request->file("pdf_file")->getClientOriginalName() .
                    "\n\nİçerik: " .
                    $text,
                "embedding" => json_encode($embedding),
                "created_at" => now(),
                "updated_at" => now(),
            ]);

            return response()->json([
                "status" => "success",
                "message" =>
                    "PDF başarıyla tek bir hafıza kaydı olarak eklendi.",
            ]);
        } catch (\Exception $e) {
            Log::critical("PDF İşleme Hatası", ["msg" => $e->getMessage()]);
            return response()->json(
                ["error" => "Hata: " . $e->getMessage()],
                500
            );
        }
    }
    // Ürün veya Hizmet Güncelleme
    public function updateService(Request $request)
    {
        if (!can("whatsapp_services", "update")) {
            return response()->json(["error" => "yetki"], 403);
        }
        $request->validate([
            "id" => "required|integer",
            "title" => "required|string",
            "content" => "required|string",
        ]);

        $companyId = auth()->user()->company_id;
        $company = DB::table("companies")
            ->where("id", $companyId)
            ->first();
        $apiKey = $company->openai_api_key;

        try {
            // 1. Yeni İçerik İçin Yeni Vektör Al (ZORUNLU)
            $response = Http::withToken($apiKey)->post(
                "https://api.openai.com/v1/embeddings",
                [
                    "model" => "text-embedding-3-small",
                    "input" => "Başlık: {$request->title}\nİçerik: {$request->content}",
                ]
            );

            if (!$response->successful()) {
                throw new \Exception("Vektör güncellenemedi.");
            }

            $embedding = $response->json()["data"][0]["embedding"];

            // 2. Veritabanını Güncelle
            DB::table("knowledge_bases")
                ->where("id", $request->id)
                ->where("company_id", $companyId)
                ->update([
                    "title" => $request->title,
                    "content" => $request->content,
                    "embedding" => json_encode($embedding),
                    "updated_at" => now(),
                ]);

            return response()->json(["status" => "success"]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
    // Servis Sil
    public function destroyService(...$params)
    {
        try {
            if (!can("whatsapp_services", "delete")) {
                return response()->json(["error" => "yetki"], 403);
            }
            $id = end($params);
            $companyId = auth()->user()->company_id;

            $deleted = DB::table("knowledge_bases")
                ->where("id", $id)
                ->where("company_id", $companyId)
                ->delete();

            if ($deleted) {
                return response()->json(["status" => "success"]);
            }

            return response()->json(
                [
                    "status" => "error",
                    "message" =>
                        "Kayıt bulunamadı veya yetkiniz yok. ID: " . $id,
                ],
                404
            );
        } catch (\Exception $e) {
            Log::error("Silme Hatası: " . $e->getMessage());
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
    // Kişi Bazlı Raporlar Sayfası
    public function personReports($domain)
    {
        if (!can("whatsapp-management/reports/person-reports", "read")) {
           return view('no-authority');
        }
        $companyId = auth()->user()->company_id;

        // 1. GENEL İSTATİSTİKLER
        $generalStats = DB::table("whatsapp_contacts")
            ->where("company_id", $companyId)
            ->select([
                DB::raw("count(*) as total_people"),
                DB::raw(
                    "(SELECT count(*) FROM whatsapp_contacts WHERE company_id = $companyId AND created_at >= NOW() - INTERVAL 7 DAY) as new_people_7d"
                ),
                DB::raw(
                    "(SELECT count(*) FROM whatsapp_contacts c WHERE c.company_id = $companyId AND NOT EXISTS (SELECT 1 FROM whatsapp_messages m WHERE m.contact_id = c.id)) as passive_people"
                ),
            ])
            ->first();

        // 2. MÜŞTERİ DEĞER MATRİSİ (Loyalty Matrix)
        $loyaltyMatrix = DB::table("whatsapp_contacts")
            ->join(
                "whatsapp_messages",
                "whatsapp_contacts.id",
                "=",
                "whatsapp_messages.contact_id"
            )
            ->where("whatsapp_contacts.company_id", $companyId)
            ->select([
                "whatsapp_contacts.id",
                "whatsapp_contacts.name",
                "whatsapp_contacts.phone",
                DB::raw("count(whatsapp_messages.id) as total_messages"),
                DB::raw("max(whatsapp_messages.created_at) as last_seen"),
                DB::raw("sum(whatsapp_messages.token_usage) as total_tokens"),
                // CSAT null ise 3 (Orta) kabul et, değilse ortalamasını al
                DB::raw(
                    "COALESCE(AVG(whatsapp_messages.predicted_csat), 3) as avg_happiness"
                ),
            ])
            ->groupBy(
                "whatsapp_contacts.id",
                "whatsapp_contacts.name",
                "whatsapp_contacts.phone"
            )
            ->orderBy("total_messages", "desc")
            ->limit(15)
            ->get();

        // 3. SEGMENTASYON
        $segments = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->select("contact_id", DB::raw("count(*) as count"))
            ->groupBy("contact_id")
            ->get();

        $segmentation = [
            "new" => $segments->whereBetween("count", [1, 5])->count(),
            "regular" => $segments->whereBetween("count", [6, 20])->count(),
            "loyal" => $segments->where("count", ">", 20)->count(),
        ];

        // 4. CHURN RISK (Kayıp Riski) - TEST İÇİN 24 SAAT YAPILDI
        // Gerçek kullanımda 'INTERVAL 15 DAY' yapınız.
        $churnRisk = DB::table("whatsapp_contacts")
            ->join(
                "whatsapp_messages",
                "whatsapp_contacts.id",
                "=",
                "whatsapp_messages.contact_id"
            )
            ->where("whatsapp_contacts.company_id", $companyId)
            ->select(
                "whatsapp_contacts.name",
                "whatsapp_contacts.phone",
                DB::raw("max(whatsapp_messages.created_at) as last_date")
            )
            ->groupBy(
                "whatsapp_contacts.id",
                "whatsapp_contacts.name",
                "whatsapp_contacts.phone"
            )
            ->havingRaw(
                "max(whatsapp_messages.created_at) < NOW() - INTERVAL 24 HOUR"
            )
            ->limit(5)
            ->get();

        // 6. SAATLİK TRAFİK
        $hourlyDistribution = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->where("type", "incoming")
            ->select(
                DB::raw("HOUR(created_at) as hour"),
                DB::raw("count(*) as count")
            )
            ->groupBy("hour")
            ->orderBy("hour")
            ->get();

        // 7. ORTALAMA YANIT HIZI (Genel)
        $responseTime = DB::table("whatsapp_messages as m1")
            ->join("whatsapp_messages as m2", function ($join) {
                $join
                    ->on("m1.session_id", "=", "m2.session_id")
                    ->where("m1.type", "incoming")
                    ->where("m2.type", "outgoing")
                    ->whereRaw("m2.created_at > m1.created_at");
            })
            ->where("m1.company_id", $companyId)
            ->select(
                DB::raw(
                    "AVG(TIMESTAMPDIFF(SECOND, m1.created_at, m2.created_at)) as avg_speed"
                )
            )
            ->first();

        // 8. DİNAMİK OPERASYONEL İÇGÖRÜ HESAPLAMALARI

        // A) En Yoğun Saat Aralığı
        $peakHourData = $hourlyDistribution->sortByDesc("count")->first();
        $peakHourInfo = $peakHourData
            ? sprintf(
                "%02d:00 - %02d:00",
                $peakHourData->hour,
                $peakHourData->hour + 1
            )
            : "Veri Yok";

        // B) Haftalık Büyüme (Bu hafta vs Geçen Hafta Mesaj Sayısı)
        $thisWeekCount = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->where("created_at", ">=", now()->subDays(7))
            ->count();
        $lastWeekCount = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->whereBetween("created_at", [
                now()->subDays(14),
                now()->subDays(7),
            ])
            ->count();

        $growthRate = 0;
        if ($lastWeekCount > 0) {
            $growthRate =
                (($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100;
        } elseif ($thisWeekCount > 0) {
            $growthRate = 100; // Önceki hafta 0 ise %100 artış
        }

        $operationalInsights = [
            "peak_hour" => $peakHourInfo,
            "growth_rate" => round($growthRate, 1),
            "this_week_count" => $thisWeekCount,
        ];

        // 9. DİĞER TABLOLAR (Günlük Hacim, Kişi Bazlı Hız vb.)
        $dailyVolume = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->where("created_at", ">=", now()->subDays(15))
            ->select(
                DB::raw("DATE(created_at) as date"),
                DB::raw("count(*) as count")
            )
            ->groupBy("date")
            ->orderBy("date", "asc")
            ->get();

        $tokenAnalysis = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->select(
                "contact_id",
                DB::raw("SUM(token_usage) as total_tokens"),
                DB::raw("COUNT(id) as total_messages"),
                DB::raw("SUM(token_usage)/COUNT(id) as token_per_message")
            )
            ->groupBy("contact_id")
            ->get();

        // Günlük
        $dailyStats = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->whereDate("created_at", today())
            ->select(
                DB::raw("AVG(predicted_csat) as avg_csat"),
                DB::raw("COUNT(*) as total"),
                DB::raw(
                    'SUM(CASE WHEN sentiment="Pozitif" THEN 1 ELSE 0 END) as positive'
                ),
                DB::raw(
                    'SUM(CASE WHEN sentiment="Negatif" THEN 1 ELSE 0 END) as negative'
                )
            )
            ->first();

        // Aylık
        $monthlyStats = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->whereMonth("created_at", now()->month)
            ->whereYear("created_at", now()->year)
            ->select(
                DB::raw("AVG(predicted_csat) as avg_csat"),
                DB::raw("COUNT(*) as total"),
                DB::raw(
                    'SUM(CASE WHEN sentiment="Pozitif" THEN 1 ELSE 0 END) as positive'
                ),
                DB::raw(
                    'SUM(CASE WHEN sentiment="Negatif" THEN 1 ELSE 0 END) as negative'
                )
            )
            ->first();

        // Yıllık
        $yearlyStats = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->whereYear("created_at", now()->year)
            ->select(
                DB::raw("AVG(predicted_csat) as avg_csat"),
                DB::raw("COUNT(*) as total"),
                DB::raw(
                    'SUM(CASE WHEN sentiment="Pozitif" THEN 1 ELSE 0 END) as positive'
                ),
                DB::raw(
                    'SUM(CASE WHEN sentiment="Negatif" THEN 1 ELSE 0 END) as negative'
                )
            )
            ->first();

        $dailyCsat = $dailyStats->avg_csat ?? 0;
        $monthlyCsat = $monthlyStats->avg_csat ?? 0;
        $yearlyCsat = $yearlyStats->avg_csat ?? 0;

        return view(
            "whatsaap-management.reports.person-reports",
            compact(
                "generalStats",
                "loyaltyMatrix",
                "segmentation",
                "churnRisk",
                "hourlyDistribution",
                "responseTime",
                "tokenAnalysis",
                "dailyVolume",
                "operationalInsights",
                "dailyCsat",
                "monthlyCsat",
                "yearlyCsat"
            )
        );
    }
    // Mesaj Bazlı Raporlar Sayfası
    public function messageReports($domain)
    {
        if (!can("whatsapp-management/reports/message-reports", "read")) {
           return view('no-authority');
        }
        $companyId = auth()->user()->company_id;

        // Genel Mesaj Hacmi & AI Analizleri
        $stats = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->select([
                DB::raw("count(*) as total_messages"),
                DB::raw(
                    'count(case when type="incoming" then 1 end) as incoming_count'
                ),
                DB::raw(
                    'count(case when type="outgoing" then 1 end) as outgoing_count'
                ),
                DB::raw(
                    "count(case when created_at >= NOW() - INTERVAL 30 DAY then 1 end) as last_30_days_count"
                ),
                DB::raw("avg(length(message)) as avg_char_count"),
                DB::raw(
                    "count(case when is_fallback=1 then 1 end) as fallback_count"
                ),
                DB::raw(
                    "count(case when is_handover=1 then 1 end) as handover_count"
                ),
                DB::raw("sum(token_usage) as total_tokens"),
            ])
            ->first();

        // Günlük Mesaj Trafiği (Son 30 Gün)
        $dailyVolume = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->where("created_at", ">=", now()->subDays(30))
            ->select([
                DB::raw("DATE(created_at) as date"),
                DB::raw(
                    'count(case when type="incoming" then 1 end) as incoming'
                ),
                DB::raw(
                    'count(case when type="outgoing" then 1 end) as outgoing'
                ),
            ])
            ->groupBy("date")
            ->orderBy("date", "asc")
            ->get();

        // Aylık Mesaj Trafiği (Son 12 Ay)
        $monthlyVolume = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->select([
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw(
                    'count(case when type="incoming" then 1 end) as incoming'
                ),
                DB::raw(
                    'count(case when type="outgoing" then 1 end) as outgoing'
                ),
            ])
            ->groupBy("month")
            ->orderBy("month", "asc")
            ->get();

        //  Yıllık Mesaj Trafiği (Son 5 Yıl)
        $yearlyVolume = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->select([
                DB::raw("YEAR(created_at) as year"),
                DB::raw(
                    'count(case when type="incoming" then 1 end) as incoming'
                ),
                DB::raw(
                    'count(case when type="outgoing" then 1 end) as outgoing'
                ),
            ])
            ->groupBy("year")
            ->orderBy("year", "asc")
            ->get();

        //  Mesaj Uzunluğu Dağılımı
        $messageLengths = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->select([
                DB::raw(
                    "count(case when length(message) <= 50 then 1 end) as short_msg"
                ),
                DB::raw(
                    "count(case when length(message) > 50 and length(message) <= 200 then 1 end) as medium_msg"
                ),
                DB::raw(
                    "count(case when length(message) > 200 then 1 end) as long_msg"
                ),
            ])
            ->first();

        //  AI Analizleri: Sentiment
        $sentimentCounts = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->select([
                DB::raw(
                    'count(case when sentiment="positive" then 1 end) as positive'
                ),
                DB::raw(
                    'count(case when sentiment="neutral" then 1 end) as neutral'
                ),
                DB::raw(
                    'count(case when sentiment="negative" then 1 end) as negative'
                ),
            ])
            ->first();

        //  AI Analizleri: Intent
        $intentCounts = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->select([DB::raw("intent"), DB::raw("count(*) as count")])
            ->groupBy("intent")
            ->get();

        // Yanıtsız (Fallback) Mesajlar
        $fallbackMessages = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->where("is_fallback", 1)
            ->orderBy("created_at", "desc")
            ->get();

        // İnsan Müdahale Gerektirenler (Handover)
        $handoverMessages = DB::table("whatsapp_messages as wm")
            ->join("whatsapp_contacts as wc", "wc.id", "=", "wm.contact_id")
            ->where("wm.company_id", $companyId)
            ->where("wm.is_handover", 1)
            ->select([
                "wm.id",
                "wc.name as customer_name",
                "wc.phone as customer_phone",
                "wm.session_id",
                "wm.message",
                "wm.intent",
                "wm.sentiment",
                "wm.tone",
                "wm.predicted_csat",
                "wm.created_at",
            ])
            ->orderBy("wm.created_at", "desc")
            ->get();

        //  Keyword Analizi
        $keywords = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->pluck("keywords");

        $allKeywords = [];
        foreach ($keywords as $kwJson) {
            $arr = json_decode($kwJson, true) ?? [];
            $allKeywords = array_merge($allKeywords, $arr);
        }
        $keywordCounts = array_count_values($allKeywords);
        arsort($keywordCounts); // En çoktan en aza

        //  CSAT / Müşteri Memnuniyeti
        $csat = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->select([
                DB::raw("avg(predicted_csat) as avg_csat"),
                DB::raw(
                    "count(case when predicted_csat >=4 then 1 end) as satisfied"
                ),
                DB::raw(
                    "count(case when predicted_csat <4 then 1 end) as unsatisfied"
                ),
            ])
            ->first();

        // Token / Maliyet Analizi
        $tokenUsage = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->select([
                DB::raw("sum(token_usage) as total_tokens"),
                DB::raw("avg(token_usage) as avg_tokens_per_msg"),
            ])
            ->first();

        return view(
            "whatsaap-management.reports.messages-reports",
            compact(
                "stats",
                "dailyVolume",
                "monthlyVolume",
                "yearlyVolume",
                "messageLengths",
                "sentimentCounts",
                "intentCounts",
                "fallbackMessages",
                "handoverMessages",
                "keywordCounts",
                "csat",
                "tokenUsage"
            )
        );
    }
    // Duygu Analizi Rapor Sayfası
    public function emotionAnalysisReport($domain)
    {
        if (!can("whatsapp-management/reports/emotion-analysis", "read")) {
           return view('no-authority');
        }
        $companyId = auth()->user()->company_id;

        $daily = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->selectRaw(
                '
            DATE(created_at) as period,
            COUNT(*) total,
            SUM(sentiment="Pozitif") positive,
            SUM(sentiment="Negatif") negative,
            SUM(sentiment="Nötr") neutral,
            AVG(predicted_csat) avg_csat,
            AVG(CASE WHEN sentiment="Pozitif" THEN 1 WHEN sentiment="Negatif" THEN -1 ELSE 0 END) emotion_score
        '
            )
            ->groupBy("period")
            ->orderBy("period")
            ->get();

        $monthly = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->selectRaw(
                '
            DATE_FORMAT(created_at,"%Y-%m") as period,
            COUNT(*) total,
            SUM(sentiment="Pozitif") positive,
            SUM(sentiment="Negatif") negative,
            SUM(sentiment="Nötr") neutral,
            AVG(predicted_csat) avg_csat,
            AVG(CASE WHEN sentiment="Pozitif" THEN 1 WHEN sentiment="Negatif" THEN -1 ELSE 0 END) emotion_score
        '
            )
            ->groupBy("period")
            ->orderBy("period")
            ->get();

        $yearly = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->selectRaw(
                '
            YEAR(created_at) as period,
            COUNT(*) total,
            SUM(sentiment="Pozitif") positive,
            SUM(sentiment="Negatif") negative,
            SUM(sentiment="Nötr") neutral,
            AVG(predicted_csat) avg_csat,
            AVG(CASE WHEN sentiment="Pozitif" THEN 1 WHEN sentiment="Negatif" THEN -1 ELSE 0 END) emotion_score
        '
            )
            ->groupBy("period")
            ->orderBy("period")
            ->get();

        $systemHealth = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->selectRaw(
                '
            COUNT(*) total,
            AVG(predicted_csat) avg_csat,
            SUM(is_fallback=1)/COUNT(*)*100 fallback_rate,
            SUM(is_handover=1)/COUNT(*)*100 handover_rate,
            AVG(token_usage) avg_token,
            AVG(CASE WHEN sentiment="Pozitif" THEN 1 WHEN sentiment="Negatif" THEN -1 ELSE 0 END) emotion_score
        '
            )
            ->first();

        $intentStats = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->select("intent", DB::raw("COUNT(*) as total"))
            ->groupBy("intent")
            ->orderByDesc("total")
            ->get();
        $alert = false;
        if (
            $systemHealth->fallback_rate > 15 ||
            $systemHealth->emotion_score < -0.2
        ) {
            $alert = true;
        }

        return view(
            "whatsaap-management.reports.emotion-analysis",
            compact(
                "daily",
                "monthly",
                "yearly",
                "systemHealth",
                "intentStats",
                "alert"
            )
        );
    }
    // Anahtar Kelimeler Rapor Sayfası
    public function keywordAnalysisReport($domain)
    {
        if (!can("whatsapp-management/reports/keyword-analysis", "read")) {
           return view('no-authority');
        }
        $companyId = auth()->user()->company_id;

        $rows = DB::table("whatsapp_messages")
            ->where("company_id", $companyId)
            ->whereNotNull("keywords")
            ->select(
                "keywords",
                "sentiment",
                "intent",
                "predicted_csat",
                "is_fallback",
                "is_handover",
                "created_at"
            )
            ->get();

        $stats = [];

        foreach ($rows as $row) {
            $keywords = json_decode($row->keywords, true);
            if (!is_array($keywords)) {
                continue;
            }

            foreach ($keywords as $kw) {
                $kw = mb_strtolower(trim($kw));

                if (!isset($stats[$kw])) {
                    $stats[$kw] = [
                        "count" => 0,
                        "positive" => 0,
                        "negative" => 0,
                        "neutral" => 0,
                        "csat_total" => 0,
                        "csat_count" => 0,
                        "fallback" => 0,
                        "handover" => 0,
                        "intent" => [],
                        "daily" => [],
                    ];
                }

                $s = &$stats[$kw];
                $s["count"]++;

                match ($row->sentiment) {
                    "Pozitif" => $s["positive"]++,
                    "Negatif" => $s["negative"]++,
                    default => $s["neutral"]++,
                };

                if ($row->predicted_csat) {
                    $s["csat_total"] += $row->predicted_csat;
                    $s["csat_count"]++;
                }

                if ($row->is_fallback) {
                    $s["fallback"]++;
                }
                if ($row->is_handover) {
                    $s["handover"]++;
                }

                if ($row->intent) {
                    $s["intent"][$row->intent] =
                        ($s["intent"][$row->intent] ?? 0) + 1;
                }

                $day = Carbon::parse($row->created_at)->format("Y-m-d");
                $s["daily"][$day] = ($s["daily"][$day] ?? 0) + 1;
            }
        }

        foreach ($stats as &$k) {
            $t = max($k["count"], 1);

            $k["positive_pct"] = round(($k["positive"] / $t) * 100, 1);
            $k["negative_pct"] = round(($k["negative"] / $t) * 100, 1);
            $k["neutral_pct"] = round(($k["neutral"] / $t) * 100, 1);

            $k["emotion_score"] = round(
                ($k["positive"] - $k["negative"]) / $t,
                2
            );
            $k["avg_csat"] = $k["csat_count"]
                ? round($k["csat_total"] / $k["csat_count"], 2)
                : null;

            $k["fallback_rate"] = round(($k["fallback"] / $t) * 100, 1);
            $k["handover_rate"] = round(($k["handover"] / $t) * 100, 1);

            $k["keyword_score"] = round(
                $k["positive_pct"] -
                    $k["negative_pct"] +
                    ($k["avg_csat"] ?? 0) * 10 -
                    $k["handover_rate"] * 2,
                2
            );
        }

        $collection = collect($stats);

        $topKeywords = $collection->sortByDesc("count")->take(20);

        $mostPositiveKeywords = $collection
            ->filter(fn($k) => $k["positive_pct"] > 60 && $k["count"] > 10)
            ->sortByDesc("positive_pct")
            ->take(15);

        $problematicKeywords = $collection
            ->filter(fn($k) => $k["negative_pct"] > 40)
            ->sortByDesc("negative_pct")
            ->take(15);

        $highestBusinessImpact = $collection
            ->sortByDesc("keyword_score")
            ->take(15);

        $mostPositiveKeyword = $collection
            ->sortByDesc("emotion_score")
            ->map(
                fn($k, $word) => [
                    "word" => $word,
                    "score" => $k["emotion_score"],
                ]
            )
            ->first();

        $wordCloud = $collection
            ->map(
                fn($k, $word) => [
                    "word" => $word,
                    "weight" => $k["count"],
                    "sentiment" => $k["emotion_score"],
                ]
            )
            ->values();

        return view(
            "whatsaap-management.reports.keywords-analysis",
            compact(
                "topKeywords",
                "mostPositiveKeywords",
                "problematicKeywords",
                "highestBusinessImpact",
                "wordCloud",
                "mostPositiveKeyword"
            )
        );
    }
}
