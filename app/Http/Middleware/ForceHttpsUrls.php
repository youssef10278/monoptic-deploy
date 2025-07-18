<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ForceHttpsUrls
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Force HTTPS URLs in production
        if (app()->environment('production')) {
            URL::forceScheme('https');
            
            // Ensure proper root URL
            $appUrl = config('app.url');
            if ($appUrl && filter_var($appUrl, FILTER_VALIDATE_URL)) {
                URL::forceRootUrl($appUrl);
            }
        }

        return $next($request);
    }
}
