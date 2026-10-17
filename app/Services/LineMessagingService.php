<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineMessagingService
{
    protected ?string $token;

    public function __construct()
    {
        $this->token = config('services.line.token') ?? env('LINE_NOTIFY_TOKEN') ?? env('LINE_CHANNEL_TOKEN');
    }

    /**
     * Send notification message to LINE.
     */
    public function send(string $message): bool
    {
        if (empty($this->token)) {
            // Log as simulation if token is not configured yet
            Log::info("[LINE Notify Simulation]: {$message}");
            return true;
        }

        try {
            // Support LINE Messaging API broadcast/push or standard LINE webhook
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->token}",
            ])->post('https://notify-api.line.me/api/notify', [
                'message' => $message,
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("[LINE Notify Error]: " . $e->getMessage());
            return false;
        }
    }
}
