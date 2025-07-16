<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Créer une nouvelle vente
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
                'client_id' => 'required|exists:clients,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
            ], [
                'client_id.required' => 'Le client est requis',
                'client_id.exists' => 'Le client sélectionné n\'existe pas',
                'items.required' => 'Au moins un produit est requis',
                'items.*.product_id.required' => 'L\'ID du produit est requis',
                'items.*.product_id.exists' => 'Un des produits sélectionnés n\'existe pas',
                'items.*.quantity.required' => 'La quantité est requise',
                'items.*.quantity.min' => 'La quantité doit être d\'au moins 1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Vérifier que le client appartient au tenant
            $client = Client::where('id', $request->client_id)
                ->where('tenant_id', $user->tenant_id)
                ->first();

            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client non trouvé ou non autorisé'
                ], 422);
            }

            // Utiliser une transaction pour garantir la cohérence
            $sale = DB::transaction(function () use ($request, $user) {
                $totalAmount = 0;
                $saleItems = [];

                // Vérifier la disponibilité des produits et calculer le total
                foreach ($request->items as $item) {
                    $product = Product::where('id', $item['product_id'])
                        ->where('tenant_id', $user->tenant_id)
                        ->first();

                    if (!$product) {
                        throw new \Exception("Produit non trouvé ou non autorisé: {$item['product_id']}");
                    }

                    if ($product->quantity < $item['quantity']) {
                        throw new \Exception("Stock insuffisant pour le produit '{$product->name}'. Stock disponible: {$product->quantity}");
                    }

                    $lineTotal = $product->selling_price * $item['quantity'];
                    $totalAmount += $lineTotal;

                    $saleItems[] = [
                        'product' => $product,
                        'quantity' => $item['quantity'],
                        'price' => $product->selling_price,
                        'subtotal' => $lineTotal
                    ];
                }

                // Créer la vente
                $sale = Sale::create([
                    'total_amount' => $totalAmount,
                    'client_id' => $request->client_id,
                    'user_id' => $user->id,
                    'tenant_id' => $user->tenant_id,
                ]);

                // Créer les lignes de vente et décrémenter le stock
                foreach ($saleItems as $saleItem) {
                    // Créer la ligne de vente
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $saleItem['product']->id,
                        'quantity' => $saleItem['quantity'],
                        'price' => $saleItem['price'],
                    ]);

                    // Décrémenter le stock du produit
                    $saleItem['product']->decrement('quantity', $saleItem['quantity']);
                }

                return $sale;
            });

            // Charger les relations pour la réponse
            $sale->load(['client', 'user', 'saleItems.product']);

            return response()->json([
                'success' => true,
                'message' => 'Vente créée avec succès',
                'data' => $sale
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la vente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher la liste des ventes du tenant
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

            $sales = Sale::where('tenant_id', $user->tenant_id)
                ->with(['client', 'user', 'saleItems.product'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $sales
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des ventes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
