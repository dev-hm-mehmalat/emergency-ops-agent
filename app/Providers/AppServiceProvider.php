<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CRMService;
use App\Services\SaasService;
use App\Services\RocketChatService;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CRMService::class, function ($app) {
            return new CRMService();
        });

        $this->app->singleton(SaasService::class, function ($app) {
            return new SaasService();
        });

        $this->app->singleton(RocketChatService::class, function ($app) {
            return new RocketChatService();
        });

        if ($this->app->environment('local', 'testing', 'staging')) {
            $this->app->register(\Laravel\Dusk\DuskServiceProvider::class);
        }
    }

  

    public function boot()
    {
        Log::info('SAAS_API_URL from AppServiceProvider: ' . env('SAAS_API_URL'));
        Log::info('SAAS_API_UPDATE_URL from AppServiceProvider: ' . env('SAAS_API_UPDATE_URL'));
        Log::info('SAAS_LOGIN_URL from AppServiceProvider: ' . env('SAAS_LOGIN_URL'));
        Log::info('SAAS_USERNAME from AppServiceProvider: ' . env('SAAS_USERNAME'));
        Log::info('SAAS_PASSWORD from AppServiceProvider: ' . env('SAAS_PASSWORD'));
    }

}
