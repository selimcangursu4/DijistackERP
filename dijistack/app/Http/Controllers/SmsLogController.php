<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SmsLog;
use Illuminate\Support\Facades\Auth;
use App\Services\SmsService;

class SmsLogController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function singleSmsStore(Request $request, $domain)
    {

        $validated = $request->validate([
            'phone'          => 'required|string',
            'message'        => 'required|string',
            'module_id'      => 'required|integer',
            'record_id'      => 'nullable|integer',
            'recipient_name' => 'nullable|string'
        ]);

        try {
            // SMS Gönderme (şimdilik devre dışı)
            // $smsResponse = $this->smsService->send(
            //     $validated['phone'],
            //     $validated['message']
            // );

            $status = 'Beklemede';
            $log = SmsLog::create([
                'company_id'        => Auth::user()->company_id ?? 1,
                'module_id'         => $validated['module_id'],
                'module_record_id'  => $validated['record_id'] ?? null,
                'recipient_name'    => $validated['recipient_name'] ?? null,
                'recipient_phone'   => $validated['phone'],
                'message'           => $validated['message'],
                'sent_by'           => Auth::id(),
                'status'            => $status,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'SMS logu başarıyla oluşturuldu (gönderim kapalı).',
                'data' => $log
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
