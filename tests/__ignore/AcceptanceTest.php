<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AcceptanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_user_registration_and_login(): void
    {
        // Schritt 1: API-Registrierung
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Debug-Ausgabe bei Fehler
        if (!in_array($response->status(), [200, 201])) {
            dump('Registrierung fehlgeschlagen');
            dump('Statuscode:', $response->status());
            dump('Antwort:', $response->json());
        }

        // Registrierung erfolgreich?
        $this->assertTrue(in_array($response->status(), [200, 201]));

        // Benutzer in DB vorhanden?
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);

        // Schritt 2: API-Login
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Debug-Ausgabe bei Fehler
        if (!in_array($response->status(), [200, 201])) {
            dump('Login fehlgeschlagen');
            dump('Statuscode:', $response->status());
            dump('Antwort:', $response->json());
        }

        // Login erfolgreich?
        $this->assertTrue(in_array($response->status(), [200, 201]));

        // Authentifizierung prüfen (optional, je nach Setup)
        // $this->assertAuthenticated(); ← funktioniert nur bei session-basiertem Login
    }
}