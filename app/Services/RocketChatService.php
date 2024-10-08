<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RocketChatService
{
    protected $baseUrl;
    protected $token;
    protected $userId;

    public function __construct()
    {
        $this->baseUrl = env('ROCKET_CHAT_URL', 'https://your.rocket.chat.server/api/v1');
        $this->token = env('ROCKET_CHAT_API_KEY');
        $this->userId = env('ROCKET_CHAT_USER_ID');

        Log::debug('Loaded ROCKET_CHAT_URL: ' . $this->baseUrl);
    }

    public function sendMessage($message)
    {
        if (env('APP_ENV') === 'local') {
            Log::debug('Simulating sending message to Rocket Chat in development environment');
            Log::debug('Message: ' . $message);
            return true;
        }

        $url = $this->baseUrl . '/chat.postMessage';
        Log::debug('Sending request to Rocket Chat API', ['url' => $url, 'message' => $message]);

        $response = Http::withHeaders([
            'X-Auth-Token' => $this->token,
            'X-User-Id' => $this->userId,
            'Content-Type' => 'application/json'
        ])->post($url, [
            'channel' => '#general',
            'text' => $message
        ]);

        if ($response->successful()) {
            Log::debug('Message sent to Rocket Chat successfully');
            return true;
        } else {
            Log::error('Failed to send message to Rocket Chat', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \Exception('Failed to send message to Rocket Chat');
        }
    }
}
