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
            $appUrl = config('app.url');

            // Only set if we have a valid URL
            if ($appUrl && filter_var($appUrl, FILTER_VALIDATE_URL)) {
                // Parse URL to ensure it's properly formatted
                $parsed = parse_url($appUrl);
                if ($parsed && isset($parsed['scheme'], $parsed['host'])) {
                    URL::forceScheme($parsed['scheme']);
                    URL::forceRootUrl($appUrl);
                }
            } else {
                // Fallback to HTTPS scheme only
                URL::forceScheme('https');
            }
        }

        return $next($request);
    }
}
