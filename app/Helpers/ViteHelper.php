<?php

namespace App\Helpers;

class ViteHelper
{
    /**
     * Fix Vite asset URLs to prevent double protocol
     */
    public static function fixAssetUrl($url)
    {
        // Remove double protocol if present
        $url = preg_replace('/https:\/\/https\/\//', 'https://', $url);
        $url = preg_replace('/http:\/\/https\/\//', 'https://', $url);
        
        return $url;
    }
    
    /**
     * Generate proper asset URL
     */
    public static function asset($path)
    {
        $baseUrl = config('app.url');
        
        // Ensure no double slashes
        $path = ltrim($path, '/');
        $baseUrl = rtrim($baseUrl, '/');
        
        return $baseUrl . '/' . $path;
    }
}
