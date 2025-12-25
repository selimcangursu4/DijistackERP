<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WhatsAppChatController extends Controller
{
    // Sohbet listesi
    public function index()
    {
        $companyId = auth()->user()->company_id;

        $latestMessages = DB::table('whatsapp_messages as wm')
            ->join('whatsapp_contacts as wc', 'wc.id', '=', 'wm.contact_id')
            ->where('wm.company_id', $companyId)
            ->select(
                'wm.session_id',
                'wc.name as customer_name',
                'wc.phone as customer_phone',
                DB::raw('MAX(wm.created_at) as last_message_time'),
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(wm.message ORDER BY wm.created_at DESC), ",", 1) as last_message')
            )
            ->groupBy('wm.session_id', 'wc.name', 'wc.phone')
            ->orderBy('last_message_time', 'desc')
            ->get();

        return view('whatsaap-management.chat.index', compact('latestMessages'));
    }

    // Tek bir sohbeti getir

    public function show($sessionId)
{
    $companyId = auth()->user()->company_id;

    $messages = DB::table('whatsapp_messages')
        ->where('company_id', $companyId)
        ->where('session_id', $sessionId)
        ->orderBy('created_at', 'asc')
        ->get();

    if ($messages->isEmpty()) {
        return response()->json([
            'contact' => null,
            'messages' => []
        ]);
    }

    $contact = DB::table('whatsapp_contacts')
        ->where('id', $messages->first()->contact_id)
        ->first();

    return response()->json([
        'contact' => $contact,
        'messages' => $messages->map(function($msg){
            return [
                'message' => $msg->message,
                'created_at' => $msg->created_at,
                'type' => $msg->type 
            ];
        })
    ]);
}

    // Mesaj gönder
  public function send(Request $request)
{
    $request->validate([
        'session_id' => 'required',
        'message' => 'required'
    ]);

    $companyId = auth()->user()->company_id;

    // contact_id'yi session_id'den al
    $messageContact = DB::table('whatsapp_messages')
        ->where('company_id', $companyId)
        ->where('session_id', $request->session_id)
        ->first();

    if (!$messageContact) {
        return response()->json(['error' => 'Geçersiz session_id'], 400);
    }

    // Node.js server'a mesaj gönder
    Http::post(env('WHATSAPP_NODE_URL') . '/send-message', [
        'company_id' => $companyId,
        'chat_id' => $messageContact->contact_id,
        'message' => $request->message
    ]);

    return response()->json(['status' => 'sent']);
}
}
