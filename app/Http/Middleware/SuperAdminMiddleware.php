<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier que l'utilisateur est authentifié
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        // Vérifier que l'utilisateur est Super Admin
        $user = auth()->user();
        if ($user->role->value !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Seuls les Super Administrateurs peuvent accéder à cette ressource.'
            ], 403);
        }

        return $next($request);
    }
}
