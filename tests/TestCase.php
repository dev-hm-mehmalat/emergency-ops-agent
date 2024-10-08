<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // Füge diese Methode hinzu
    protected function setUp(): void
    {
        parent::setUp();

        // Deaktiviere die CSRF-Überprüfung für Tests
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }
}
