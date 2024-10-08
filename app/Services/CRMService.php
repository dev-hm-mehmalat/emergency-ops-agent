<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CRMService
{
    protected $emergencyPlans = [];

    public function __construct()
    {
        $this->emergencyPlans = [
            [
                'name' => 'John Doe',
                'phone' => '123-456-7890',
                'email' => 'johndoe@example.com',
                'eligible_for_emergency' => true
            ],
            [
                'name' => 'Jane Doe',
                'phone' => '987-654-3210',
                'email' => 'janedoe@example.com',
                'eligible_for_emergency' => true
            ],
            [
                'name' => 'Alice Smith',
                'phone' => '555-123-4567',
                'email' => 'alicesmith@example.com',
                'eligible_for_emergency' => true
            ]
        ];
    }

    public function getData()
    {
        Log::info('Using dummy data for CRM');
        return Cache::remember('crm_data', 60, function () {
            $dummyData = $this->emergencyPlans;
            Log::info('Returning CRM dummy data:', $dummyData);

            // Simulate the API call
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('CRM_API_KEY'),
            ])->get('https://api.crm.com/data');

            if ($response->successful() && !empty($response->json())) {
                Log::debug('API response data:', $response->json());
                return $response->json();
            } else {
                Log::debug('Returning dummy data:', $dummyData);
                return $dummyData;
            }
        });
    }
}
