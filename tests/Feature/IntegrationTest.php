<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\CRMService;
use App\Services\SaasService;
use App\Services\RocketChatService;

class IntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Explicitly set environment variables
        putenv('SAAS_API_UPDATE_URL=https://desktop.saas.de/rest/api/time/overview/update');
        putenv('EXTERNAL_WEBSITE_API_URL=https://postman-echo.com/post');
        putenv('SAAS_API_URL=https://your-saas-api.com/attendance');
        putenv('SAAS_LOGIN_URL=https://your-saas-api.com/login');
        putenv('ROCKET_CHAT_URL=https://your.rocket.chat.server/api/v1');
        putenv('CRM_API_KEY=fake-key');

        // Simulate HTTP responses for testing
        Http::fake([
            env('SAAS_LOGIN_URL') => Http::response(['token' => 'fake-token'], 200),
            env('SAAS_API_URL') => Http::response([
                'John Doe' => ['present' => true],
                'Jane Doe' => ['present' => false]
            ], 200),
            env('SAAS_API_UPDATE_URL') => Http::response(['status' => 'success'], 200),
            env('EXTERNAL_WEBSITE_API_URL') => Http::response(['status' => 'success'], 200),
            env('ROCKET_CHAT_URL') . '/chat.postMessage' => Http::response(['success' => true], 200)
        ]);

        // Clear the cache
        Cache::flush();
    }

    public function testEmergencyPlanUpdate()
    {
        try {
            $crmService = new CRMService();
            $saasService = new SaasService();
            $rocketChatService = new RocketChatService();

            // Check environment variables
            $this->assertNotNull(env('SAAS_API_UPDATE_URL'), 'SAAS_API_UPDATE_URL is not set');
            $this->assertNotNull(env('EXTERNAL_WEBSITE_API_URL'), 'EXTERNAL_WEBSITE_API_URL is not set');

            // Simulate fetching data from CRM
            $crmData = $crmService->getData();
            Log::info('Fetched CRM Data: ', $crmData);
            $this->assertIsArray($crmData);
            $this->assertCount(3, $crmData); // Erwartet 3 Einträge

            // Logge die CRM-Daten
            Log::info('CRM Data:', $crmData);

            // Simulate fetching staff attendance from SaaS
            $saasData = $saasService->getStaffAttendance();
            $this->assertIsArray($saasData);
            $this->assertArrayHasKey('John Doe', $saasData);

            // Filter the emergency staff
            $emergencyStaff = array_filter($crmData, function ($staff) use ($saasData) {
                return isset($saasData[$staff['name']]) && $saasData[$staff['name']]['present'];
            });

            $this->assertNotEmpty($emergencyStaff);
            $this->assertArrayHasKey(0, $emergencyStaff); // Index 0 für den ersten Mitarbeiter in $emergencyStaff

            // Simulate sending messages to Rocket Chat
            foreach ($emergencyStaff as $staff) {
                $rocketChatService->sendMessage("Notfalldienstinformationen: Name: {$staff['name']}, Telefon: {$staff['phone']}, E-Mail: {$staff['email']}");
            }

            Http::assertSent(function ($request) {
                return $request->url() == env('ROCKET_CHAT_URL') . '/chat.postMessage' &&
                    $request['text'] == 'Notfalldienstinformationen: Name: John Doe, Telefon: 123-456-7890, E-Mail: johndoe@example.com';
            });

            // Simulate updating the emergency plan in SaaS
            $saasService->updateEmergencyPlan(['emergency_staff' => $emergencyStaff]);

            Http::assertSent(function ($request) {
                return $request->url() == env('SAAS_API_UPDATE_URL');
            });

            // Simulate sending emergency service data to the external website
            $response = Http::post(env('EXTERNAL_WEBSITE_API_URL'), [
                'emergency_service' => $emergencyStaff
            ]);

            $this->assertEquals(200, $response->status());
            $this->assertEquals(['status' => 'success'], $response->json());
        } catch (\Exception $e) {
            Log::error('Error in testEmergencyPlanUpdate: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }
}
