<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmergencyPlanController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Hier können Sie API-Routen für Ihre Anwendung registrieren. Diese Routen
| werden vom RouteServiceProvider geladen und der "api" Middleware-Gruppe
| zugewiesen. Genießen Sie die Entwicklung!
|
*/

Route::middleware('api')->group(function () {
    // **Notfallplan-Routen**

    // Abrufen aller Notfallpläne
    Route::get('/emergency-service', [EmergencyPlanController::class, 'index'])->name('api.emergency.index');

    // Erstellen eines neuen Notfallplans
    Route::post('/emergency-plan', [EmergencyPlanController::class, 'store'])->name('api.emergency.store');

    // Aktualisieren eines bestehenden Notfallplans
    Route::put('/emergency-plan/{id}', [EmergencyPlanController::class, 'update'])->name('api.emergency.update');

    // Löschen eines bestehenden Notfallplans
    Route::delete('/emergency-plan/{id}', [EmergencyPlanController::class, 'destroy'])->name('api.emergency.destroy');

    // **Authentifizierungsrouten**

    // Benutzerregistrierung
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('api.register');

    // Benutzeranmeldung
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('api.login');

    // **Cache-Clear-Route**

    // Route zum Leeren des Notfallplan-Caches
    Route::post('/cache/clear', [EmergencyPlanController::class, 'clearCache'])->name('api.cache.clear');
});
