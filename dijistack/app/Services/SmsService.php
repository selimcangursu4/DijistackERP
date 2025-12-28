<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{
    protected $apiUrl;
    protected $apiKey;
    protected $sender;

    public function __construct()
    {
        $this->apiUrl = config('services.sms.url');
        $this->apiKey = config('services.sms.key');
        $this->sender = config('services.sms.sender');
    }

    public function send($phone, $message)
    {
        $response = Http::post($this->apiUrl, [
            'api_key' => $this->apiKey,
            'sender'  => $this->sender,
            'phone'   => $phone,
            'message' => $message
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'data' => $response->json()
            ];
        }

        return [
            'success' => false,
            'message' => $response->body()
        ];
    }
}
