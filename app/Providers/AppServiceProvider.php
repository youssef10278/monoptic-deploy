<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forcer HTTPS en production (Railway)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');

            // Forcer l'URL racine pour éviter les doubles protocoles
            if (env('APP_URL')) {
                URL::forceRootUrl(env('APP_URL'));
            }
        }

        Vite::prefetch(concurrency: 3);
    }
}
