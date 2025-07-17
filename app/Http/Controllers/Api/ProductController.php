<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Afficher la liste des produits du tenant
     */
    public function index(Request $request): JsonResponse
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

            $query = Product::where('tenant_id', $user->tenant_id)
                ->with('productCategory');

            // Filtrer par catégorie si spécifiée
            if ($request->has('category_id') && $request->category_id) {
                $query->where('product_category_id', $request->category_id);
            }

            // Filtrer par type si spécifié
            if ($request->has('type') && $request->type) {
                $query->where('type', $request->type);
            }

            $products = $query->orderBy('name')->get();

            return response()->json([
                'success' => true,
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des produits',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer un nouveau produit
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            // Log pour debug
            \Log::info('ProductController::store - Début', [
                'user_id' => $user->id,
                'tenant_id' => $user->tenant_id,
                'request_data' => $request->all()
            ]);

            // Vérifier que l'utilisateur appartient à un tenant
            if (!$user->tenant_id) {
                \Log::error('ProductController::store - Utilisateur sans tenant_id', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non associé à un magasin'
                ], 403);
            }

            // Validation des données
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'brand' => 'nullable|string|max:255',
                'reference' => 'nullable|string|max:255',
                'purchase_price' => 'nullable|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'barcode' => 'nullable|string|max:255',
                'product_category_id' => 'required|integer|exists:product_categories,id,tenant_id,' . $user->tenant_id,
                'type' => 'nullable|string|in:frame,accessory,lens,contact_lens',
                'attributes' => 'nullable|array',
            ], [
                'name.required' => 'Le nom du produit est requis',
                'selling_price.required' => 'Le prix de vente est requis',
                'selling_price.numeric' => 'Le prix de vente doit être un nombre',
                'quantity.required' => 'La quantité est requise',
                'quantity.integer' => 'La quantité doit être un nombre entier',
                'product_category_id.required' => 'La catégorie est requise',
                'product_category_id.exists' => 'La catégorie sélectionnée n\'existe pas',
            ]);

            if ($validator->fails()) {
                \Log::error('ProductController::store - Validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'request_data' => $request->all()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Vérifier que la catégorie appartient au tenant
            $category = ProductCategory::where('id', $request->product_category_id)
                ->where('tenant_id', $user->tenant_id)
                ->first();

            \Log::info('ProductController::store - Vérification catégorie', [
                'category_id' => $request->product_category_id,
                'tenant_id' => $user->tenant_id,
                'category_found' => $category ? true : false
            ]);

            if (!$category) {
                // Créer des catégories par défaut si elles n'existent pas
                $this->createDefaultCategories($user->tenant_id);

                // Réessayer de trouver la catégorie
                $category = ProductCategory::where('id', $request->product_category_id)
                    ->where('tenant_id', $user->tenant_id)
                    ->first();

                if (!$category) {
                    \Log::error('ProductController::store - Catégorie non trouvée même après création par défaut', [
                        'category_id' => $request->product_category_id,
                        'tenant_id' => $user->tenant_id
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Catégorie non trouvée ou non autorisée'
                    ], 422);
                }
            }

            // Créer le produit avec le tenant_id de l'utilisateur authentifié
            $product = Product::create([
                'name' => $request->name,
                'brand' => $request->brand,
                'reference' => $request->reference,
                'purchase_price' => $request->purchase_price,
                'selling_price' => $request->selling_price,
                'quantity' => $request->quantity,
                'barcode' => $request->barcode,
                'product_category_id' => $request->product_category_id,
                'tenant_id' => $user->tenant_id,
                'type' => $request->type ?? 'accessory',
                'attributes' => $request->attributes ?? [],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produit créé avec succès',
                'data' => $product->load('productCategory')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du produit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un produit spécifique
     */
    public function show($id): JsonResponse
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

            // Récupérer le produit uniquement s'il appartient au tenant de l'utilisateur
            $product = Product::where('tenant_id', $user->tenant_id)
                ->where('id', $id)
                ->with('productCategory')
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $product
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du produit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour un produit
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

            // Récupérer le produit uniquement s'il appartient au tenant de l'utilisateur
            $product = Product::where('tenant_id', $user->tenant_id)
                ->where('id', $id)
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé'
                ], 404);
            }

            // Validation des données
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'brand' => 'nullable|string|max:255',
                'reference' => 'nullable|string|max:255',
                'purchase_price' => 'nullable|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'barcode' => 'nullable|string|max:255',
                'product_category_id' => 'required|integer|exists:product_categories,id,tenant_id,' . $user->tenant_id,
                'type' => 'nullable|string|in:frame,accessory,lens,contact_lens',
                'attributes' => 'nullable|array',
            ], [
                'name.required' => 'Le nom du produit est requis',
                'selling_price.required' => 'Le prix de vente est requis',
                'selling_price.numeric' => 'Le prix de vente doit être un nombre',
                'quantity.required' => 'La quantité est requise',
                'quantity.integer' => 'La quantité doit être un nombre entier',
                'product_category_id.required' => 'La catégorie est requise',
                'product_category_id.exists' => 'La catégorie sélectionnée n\'existe pas',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Vérifier que la catégorie appartient au tenant
            $category = ProductCategory::where('id', $request->product_category_id)
                ->where('tenant_id', $user->tenant_id)
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Catégorie non trouvée ou non autorisée'
                ], 422);
            }

            // Mettre à jour le produit
            $product->update([
                'name' => $request->name,
                'brand' => $request->brand,
                'reference' => $request->reference,
                'purchase_price' => $request->purchase_price,
                'selling_price' => $request->selling_price,
                'quantity' => $request->quantity,
                'barcode' => $request->barcode,
                'product_category_id' => $request->product_category_id,
                'type' => $request->type ?? $product->type,
                'attributes' => $request->attributes ?? $product->attributes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produit mis à jour avec succès',
                'data' => $product->fresh()->load('productCategory')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du produit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un produit
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

            // Récupérer le produit uniquement s'il appartient au tenant de l'utilisateur
            $product = Product::where('tenant_id', $user->tenant_id)
                ->where('id', $id)
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé'
                ], 404);
            }

            // Supprimer le produit
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produit supprimé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du produit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer des catégories par défaut pour un tenant
     */
    private function createDefaultCategories($tenantId)
    {
        $defaultCategories = [
            'Montures',
            'Verres',
            'Lentilles de contact',
            'Accessoires'
        ];

        foreach ($defaultCategories as $categoryName) {
            ProductCategory::firstOrCreate([
                'name' => $categoryName,
                'tenant_id' => $tenantId
            ]);
        }

        \Log::info('ProductController - Catégories par défaut créées', [
            'tenant_id' => $tenantId,
            'categories' => $defaultCategories
        ]);
    }
}
