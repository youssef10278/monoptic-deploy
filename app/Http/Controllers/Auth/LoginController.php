<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Authentifier un utilisateur et retourner un token API
     */
    public function login(Request $request): JsonResponse
    {
        // Validation des données d'entrée
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données de validation invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        // Tentative d'authentification
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect'
            ], 401);
        }

        // Récupérer l'utilisateur authentifié
        $user = Auth::user();

        // Vérifier le statut du tenant si l'utilisateur appartient à un tenant
        if ($user->tenant_id && $user->tenant) {
            $tenantStatus = $user->tenant->status;

            // Vérifier si le tenant est actif ou en période d'essai
            if (!in_array($tenantStatus, ['active', 'trial'])) {
                // Déconnecter immédiatement l'utilisateur
                Auth::logout();

                // Messages d'erreur selon le statut
                $errorMessages = [
                    'suspended' => 'Votre compte a été suspendu. Contactez l\'administrateur pour plus d\'informations.',
                    'cancelled' => 'Votre abonnement a été annulé. Contactez l\'administrateur pour réactiver votre compte.',
                    'expired' => 'Votre abonnement a expiré. Contactez l\'administrateur pour renouveler votre abonnement.'
                ];

                $message = $errorMessages[$tenantStatus] ?? 'Votre compte n\'est pas autorisé à se connecter.';

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'tenant_status' => $tenantStatus
                ], 403);
            }

            // Vérifier si l'abonnement a expiré (date de fin dépassée)
            if ($user->tenant->subscription_end_date &&
                now()->isAfter($user->tenant->subscription_end_date) &&
                $tenantStatus !== 'trial') {

                // Déconnecter immédiatement l'utilisateur
                Auth::logout();

                return response()->json([
                    'success' => false,
                    'message' => 'Votre abonnement a expiré le ' .
                             $user->tenant->subscription_end_date->format('d/m/Y') .
                             '. Contactez l\'administrateur pour renouveler votre abonnement.',
                    'tenant_status' => 'expired',
                    'expiry_date' => $user->tenant->subscription_end_date->format('Y-m-d')
                ], 403);
            }
        }

        // Créer un token d'API
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'tenant_id' => $user->tenant_id,
                    'tenant' => $user->tenant ? [
                        'id' => $user->tenant->id,
                        'name' => $user->tenant->name,
                        'status' => $user->tenant->status,
                    ] : null,
                ],
                'token' => $token,
            ]
        ], 200);
    }

    /**
     * Déconnecter l'utilisateur et révoquer le token
     */
    public function logout(Request $request): JsonResponse
    {
        // Récupérer l'utilisateur authentifié
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        // Révoquer le token actuel
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ], 200);
    }
}
