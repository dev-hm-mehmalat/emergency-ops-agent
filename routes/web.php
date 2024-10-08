<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Route für die Startseite
Route::get('/', function () {
    return view('welcome');
});

// Authentifizierungsrouten
Route::post('/register', [RegisteredUserController::class, 'store'])->name('web.register');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('web.login');

// Testroute zum Überprüfen der Umgebungsvariablen
Route::get('/test-env', function () {
    return response()->json([
        'SAAS_API_URL' => env('SAAS_API_URL'),
        'SAAS_API_UPDATE_URL' => env('SAAS_API_UPDATE_URL'),
        'SAAS_LOGIN_URL' => env('SAAS_LOGIN_URL'),
        'SAAS_USERNAME' => env('SAAS_USERNAME'),
        'SAAS_PASSWORD' => env('SAAS_PASSWORD'),
    ]);
});

// Route für das Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
