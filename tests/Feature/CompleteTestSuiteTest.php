<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompleteTestSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected $planId;

    protected function setUp(): void
    {
        parent::setUp();

        // Erstelle einen Plan vor jedem Test
        $response = $this->post('/api/emergency-plan', [
            'name' => 'Test Plan',
            'details' => 'Details for test plan',
            'email' => 'testplan@example.com',
            'phone' => '123-456-7890',
            'eligible_for_emergency' => true,
        ]);

        $this->planId = $response->json('id');
    }

    public function testApiEndpoints()
    {
        $response = $this->get('/api/emergency-service');
        $response->assertStatus(200);
    }

    public function testUserRegistration()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
    }

    public function testCreateEmergencyPlan()
    {
        $response = $this->post('/api/emergency-plan', [
            'name' => 'New Plan',
            'details' => 'Details for new plan',
            'email' => 'newplan@example.com',
            'phone' => '123-456-7890',
            'eligible_for_emergency' => true,
        ]);

        $response->assertStatus(201);
    }

    public function testUpdateEmergencyPlan()
    {
        // Verwende den in setUp() erstellten Plan zum Aktualisieren
        $response = $this->put("/api/emergency-plan/{$this->planId}", [
            'name' => 'Updated Plan',
            'details' => 'Updated details',
        ]);

        $response->assertStatus(200);
    }

    public function testDeleteEmergencyPlan()
    {
        // Lösche den in setUp() erstellten Plan
        $response = $this->delete("/api/emergency-plan/{$this->planId}");
        $response->assertStatus(200);
    }
}
