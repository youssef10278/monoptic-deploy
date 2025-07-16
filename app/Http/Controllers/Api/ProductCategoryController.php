<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    /**
     * Afficher la liste des catégories de produits du tenant
     */
    public function index(): JsonResponse
    {
        try {
            $user = auth()->user();

            // Vérifier que l'utilisateur appartient à un tenant
            if (!$user->tenant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non associé à un magasin'
                ], 403);
            }

            $categories = ProductCategory::where('tenant_id', $user->tenant_id)
                ->withCount('products')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des catégories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer une nouvelle catégorie de produits
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            // Vérifier que l'utilisateur appartient à un tenant
            if (!$user->tenant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non associé à un magasin'
                ], 403);
            }

            // Validation des données
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ], [
                'name.required' => 'Le nom de la catégorie est requis',
                'name.max' => 'Le nom ne doit pas dépasser 255 caractères',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Vérifier l'unicité du nom dans le tenant
            $existingCategory = ProductCategory::where('tenant_id', $user->tenant_id)
                ->where('name', $request->name)
                ->first();

            if ($existingCategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une catégorie avec ce nom existe déjà'
                ], 422);
            }

            // Créer la catégorie avec le tenant_id de l'utilisateur authentifié
            $category = ProductCategory::create([
                'name' => $request->name,
                'tenant_id' => $user->tenant_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Catégorie créée avec succès',
                'data' => $category->load('products')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la catégorie',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour une catégorie de produits
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = auth()->user();

            // Vérifier que l'utilisateur appartient à un tenant
            if (!$user->tenant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non associé à un magasin'
                ], 403);
            }

            // Récupérer la catégorie uniquement si elle appartient au tenant de l'utilisateur
            $category = ProductCategory::where('tenant_id', $user->tenant_id)
                ->where('id', $id)
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Catégorie non trouvée'
                ], 404);
            }

            // Validation des données
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ], [
                'name.required' => 'Le nom de la catégorie est requis',
                'name.max' => 'Le nom ne doit pas dépasser 255 caractères',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Vérifier l'unicité du nom dans le tenant (sauf pour la catégorie actuelle)
            $existingCategory = ProductCategory::where('tenant_id', $user->tenant_id)
                ->where('name', $request->name)
                ->where('id', '!=', $id)
                ->first();

            if ($existingCategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une catégorie avec ce nom existe déjà'
                ], 422);
            }

            // Mettre à jour la catégorie
            $category->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Catégorie mise à jour avec succès',
                'data' => $category->fresh()->load('products')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la catégorie',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer une catégorie de produits
     */
    public function destroy($id): JsonResponse
    {
        try {
            $user = auth()->user();

            // Vérifier que l'utilisateur appartient à un tenant
            if (!$user->tenant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non associé à un magasin'
                ], 403);
            }

            // Récupérer la catégorie uniquement si elle appartient au tenant de l'utilisateur
            $category = ProductCategory::where('tenant_id', $user->tenant_id)
                ->where('id', $id)
                ->withCount('products')
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Catégorie non trouvée'
                ], 404);
            }

            // Vérifier s'il y a des produits dans cette catégorie
            if ($category->products_count > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer une catégorie qui contient des produits'
                ], 422);
            }

            // Supprimer la catégorie
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Catégorie supprimée avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la catégorie',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
