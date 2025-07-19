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
        // Configuration spécifique pour Railway
        if ($this->app->environment('production')) {
            // Forcer HTTPS mais sans forcer l'URL racine
            URL::forceScheme('https');

            // S'assurer que l'URL de base est correcte
            $appUrl = config('app.url');
            if ($appUrl && !str_contains($appUrl, 'https//')) {
                // Seulement si l'URL est propre
                URL::forceRootUrl($appUrl);
            }
        }

        Vite::prefetch(concurrency: 3);
    }
}
