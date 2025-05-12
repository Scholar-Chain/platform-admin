<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\MessageLog;

class FonnteService
{
    protected string $baseUrl = 'https://api.fonnte.com';
    protected string $token;

    public function __construct()
    {
        $this->token = config('app.fonnte.api_token');
    }

    public function sendMessage(string $target, string $message, ?string $media = null): array
    {
        $payload = [
            'target' => $target,
            'message' => $message,
        ];

        if ($media) {
            $payload['url'] = $media;
        }

        $response = Http::withHeaders([
            'Authorization' => $this->token,
        ])->asForm()->post($this->baseUrl . '/send', $payload);

        $responseData = $response->json();

        $this->logMessage(
            channel: 'whatsapp',
            target: $target,
            message: $message,
            media: $media,
            response: $responseData,
            success: isset($responseData['status']) && $responseData['status'] == true
        );

        return $responseData;
    }

    protected function logMessage(
        string $channel,
        string $target,
        string $message,
        ?string $media,
        array $response,
        bool $success
    ): void {
        MessageLog::create([
            'channel'  => $channel,
            'target'   => $target,
            'message'  => $message,
            'media'    => $media,
            'response' => $response,
            'success'  => $success,
        ]);
    }
}
