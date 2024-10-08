<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SaasService
{
    protected $token;

    public function authenticate()
    {
        $response = Http::post(env('SAAS_LOGIN_URL'), [
            'user' => env('SAAS_USERNAME'),
            'password' => env('SAAS_PASSWORD')
        ]);

        if ($response->successful()) {
            $this->token = $response->json()['token'];
            Log::info('SaaS authenticated successfully', ['token' => $this->token]);
        } else {
            Log::error('SaaS authentication failed', ['response' => $response->json()]);
            throw new \Exception('Invalid authentication response from SaaS service');
        }
    }

    public function getStaffAttendance()
    {
        if (!$this->token) {
            $this->authenticate();
        }

        $response = Http::withToken($this->token)->get(env('SAAS_API_URL'));

        if ($response->successful()) {
            Log::info('Fetched SaaS staff attendance', ['data' => $response->json()]);
            return $response->json();
        } else {
            Log::error('Failed to fetch SaaS staff attendance', ['response' => $response->json()]);
            throw new \Exception('Failed to fetch SaaS staff attendance');
        }
    }

    public function updateEmergencyPlan(array $data)
    {
        if (!$this->token) {
            $this->authenticate();
        }

        $response = Http::withToken($this->token)->post(env('SAAS_API_UPDATE_URL'), $data);

        if ($response->successful()) {
            Log::info('Emergency plan updated successfully in SaaS', ['response' => $response->json()]);
        } else {
            Log::error('Failed to update emergency plan in SaaS', ['response' => $response->json()]);
            throw new \Exception('Failed to update emergency plan in SaaS');
        }
    }
}
