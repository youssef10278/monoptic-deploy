<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        // Vérifier si l'utilisateur a le rôle SuperAdmin
        if ($request->user()->role !== UserRole::SuperAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Seuls les Super Administrateurs peuvent accéder à cette ressource.'
            ], 403);
        }

        return $next($request);
    }
}
