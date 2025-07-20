<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FixDoubleUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'URL contient une double URL
        $uri = $request->getRequestUri();
        
        // Pattern pour détecter les URLs doublées comme /www.monopti.com/login
        if (preg_match('/\/(www\.)?monopti\.com\/(.+)/', $uri, $matches)) {
            $correctPath = '/' . $matches[2];
            return redirect($correctPath, 301);
        }
        
        return $next($request);
    }
}
