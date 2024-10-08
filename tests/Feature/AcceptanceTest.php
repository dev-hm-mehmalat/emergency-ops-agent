<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AcceptanceTest extends TestCase
{
    use RefreshDatabase;

    public function testUserRegistrationAndLogin()
    {
        try {
            // Hole das CSRF-Token
            $csrfToken = csrf_token(); // CSRF-Token generieren

            // Schritt 1: Benutzer registrieren
            $response = $this->post('/register', [
                '_token' => $csrfToken,  // CSRF-Token hier hinzufügen
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            Log::info('Response from register:', ['response' => $response->getContent()]);
            $response->assertStatus(201);  // Erwartet 201 statt 419

            // Benutzer aus der Datenbank holen
            $user = User::where('email', 'test@example.com')->first();
            Log::info('User in database: ', ['user' => $user]);
            $this->assertNotNull($user);

            // Schritt 2: Benutzer anmelden
            $response = $this->post('/login', [
                '_token' => $csrfToken,  // CSRF-Token hier hinzufügen
                'email' => 'test@example.com',
                'password' => 'password',
            ]);

            Log::info('Response from login:', ['response' => $response->getContent()]);
            $this->assertTrue(in_array($response->status(), [200, 204]));
            $this->assertAuthenticated();
        } catch (\Exception $e) {
            Log::error('Error in testUserRegistrationAndLogin: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }
}
