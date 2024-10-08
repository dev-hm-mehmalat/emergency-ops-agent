<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CRMService;
use App\Services\SaasService;
use App\Services\RocketChatService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateEmergencyPlan extends Command
{
    protected $signature = 'emergency:update-plan';
    protected $description = 'Update emergency plan daily';

    protected $crmService;
    protected $saasService;
    protected $rocketChatService;

    public function __construct(CRMService $crmService, SaasService $saasService, RocketChatService $rocketChatService)
    {
        parent::__construct();
        $this->crmService = $crmService;
        $this->saasService = $saasService;
        $this->rocketChatService = $rocketChatService;
    }

    public function handle()
    {
        Log::info('Emergency plan update started');

        // Abrufen der CRM-Daten
        $crmData = $this->crmService->getData();
        Log::debug('CRM Data fetched', ['crmData' => $crmData]);

        // Abrufen der Anwesenheitsdaten von SaaS
        $saasData = $this->saasService->getStaffAttendance();
        Log::debug('SaaS Data fetched', ['saasData' => $saasData]);

        // Datenbearbeitung: Nur Notfalldienst-Mitarbeiter, die auch anwesend sind
        $emergencyStaff = array_filter($crmData, function ($staff) use ($saasData) {
            return isset($saasData[$staff['name']]) && $saasData[$staff['name']]['present'];
        });
        Log::debug('Filtered Emergency Staff', ['emergencyStaff' => $emergencyStaff]);

        // Nachricht an Rocket Chat senden
        foreach ($emergencyStaff as $staff) {
            $this->rocketChatService->sendMessage("Notfalldienstinformationen: Name: {$staff['name']}, Telefon: {$staff['phone']}, E-Mail: {$staff['email']}");
            Log::info('Message sent to Rocket.Chat', ['staff' => $staff]);
        }

        // Notfalldienstinformationen an den SaaS-Dienst senden
        if (env('APP_ENV') === 'local') {
            Log::debug('Simulating update of emergency plan in development environment');
        } else {
            $this->saasService->updateEmergencyPlan(['emergency_staff' => $emergencyStaff]);
            Log::info('Emergency plan updated in SaaS', ['emergencyStaff' => $emergencyStaff]);
        }

        // Daten an die externe Webseite senden
        if (env('APP_ENV') === 'local') {
            Log::debug('Simulating sending emergency service data to external website');
        } else {
            $response = Http::post(env('EXTERNAL_WEBSITE_API_URL'), [
                'emergency_service' => $emergencyStaff
            ]);

            if ($response->successful()) {
                Log::info('Notfalldienstinformationen gesendet und Dienstplan aktualisiert');
            } else {
                Log::error('Fehler beim Senden der Notfalldienstinformationen', ['response' => $response->body()]);
            }
        }

        Log::info('Emergency plan update completed');
    }
}
