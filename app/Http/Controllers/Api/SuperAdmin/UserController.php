<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Mettre à jour un utilisateur (email et mot de passe)
     */
    public function update(Request $request, User $user): JsonResponse
    {
        try {
            // Validation des données
            $validator = Validator::make($request->all(), [
                'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
                'password' => 'sometimes|nullable|string|min:6',
                'name' => 'sometimes|required|string|max:255',
            ], [
                'email.unique' => 'Cette adresse email est déjà utilisée par un autre utilisateur',
                'email.email' => 'L\'adresse email doit être valide',
                'password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
                'name.required' => 'Le nom est requis',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Vérifier que l'utilisateur appartient bien à un tenant (pas un Super Admin)
            if (!$user->tenant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de modifier un Super Administrateur'
                ], 403);
            }

            // Mettre à jour les champs fournis
            if ($request->has('name')) {
                $user->name = $request->name;
            }

            if ($request->has('email')) {
                $user->email = $request->email;
            }

            if ($request->has('password') && !empty($request->password)) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // Recharger l'utilisateur avec ses relations
            $user->load(['tenant']);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur mis à jour avec succès',
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
                        'updated_at' => $user->updated_at,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
