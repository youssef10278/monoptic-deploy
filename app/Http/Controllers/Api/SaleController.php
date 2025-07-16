<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Client;
use App\Models\ContactLensBrand;
use App\Models\SaleStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Afficher la liste des ventes du tenant
     */
    public function index(): JsonResponse
    {
        try {
            $user = auth()->user();

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

    /**
     * Afficher les détails complets d'une vente
     */
    public function show($id): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user->tenant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non associé à un magasin'
                ], 403);
            }

            $sale = Sale::where('tenant_id', $user->tenant_id)
                ->where('id', $id)
                ->with([
                    'client',
                    'user',
                    'saleItems.product.productCategory',
                    'payments.user',
                    'statusHistories.user'
                ])
                ->first();

            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vente non trouvée'
                ], 404);
            }

            // Utiliser l'historique réel des statuts
            $statusHistory = $sale->statusHistories->map(function ($history) {
                return [
                    'old_status' => $history->old_status,
                    'status' => $history->new_status,
                    'created_at' => $history->changed_at,
                    'user' => $history->user,
                    'comment' => $history->comment
                ];
            });

            // Si aucun historique n'existe, créer une entrée pour la création
            if ($statusHistory->isEmpty()) {
                $statusHistory = collect([[
                    'old_status' => null,
                    'status' => $sale->status,
                    'created_at' => $sale->created_at,
                    'user' => $sale->user,
                    'comment' => 'Vente créée'
                ]]);
            }

            $sale->status_history = $statusHistory;

            // Calculer le statut de paiement
            $paymentStatus = 'unpaid';
            if ($sale->paid_amount >= $sale->total_amount) {
                $paymentStatus = 'paid';
            } elseif ($sale->paid_amount > 0) {
                $paymentStatus = 'partial';
            }

            // Ajouter les informations calculées
            $sale->payment_status = $paymentStatus;
            $sale->remaining_amount = $sale->total_amount - $sale->paid_amount;

            // Formater les articles de vente
            $sale->sale_items = $sale->saleItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product ? $item->product->name : $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'total_price' => $item->price * $item->quantity,
                    'description' => $item->description,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $sale
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des détails de la vente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer une nouvelle vente avec articles flexibles
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user->tenant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non associé à un magasin'
                ], 403);
            }

            // Validation de base
            $validator = Validator::make($request->all(), [
                'client_id' => 'nullable|integer',
                'items' => 'required|array|min:1',
                'items.*.type' => 'required|in:product,custom',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.description' => 'required|string',
                'paid_amount' => 'required|numeric|min:0',
            ], [
                'client_id.integer' => 'Le client doit être un identifiant valide',
                'client_id.exists' => 'Le client sélectionné n\'existe pas',
                'items.required' => 'Au moins un article est requis',
                'items.*.type.required' => 'Le type d\'article est requis',
                'items.*.type.in' => 'Le type d\'article doit être "product" ou "custom"',
                'items.*.quantity.required' => 'La quantité est requise',
                'items.*.quantity.min' => 'La quantité doit être d\'au moins 1',
                'items.*.price.required' => 'Le prix est requis',
                'items.*.price.min' => 'Le prix doit être positif',
                'items.*.description.required' => 'La description est requise',
                'paid_amount.required' => 'Le montant payé est requis',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Vérifier que le client appartient au même tenant (si un client est spécifié)
            $client = null;
            if ($request->client_id) {
                $client = Client::where('id', $request->client_id)
                               ->where('tenant_id', $user->tenant_id)
                               ->first();

                if (!$client) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Client non trouvé ou non autorisé'
                    ], 404);
                }
            }

            DB::beginTransaction();

            try {
                $totalAmount = 0;
                $saleItems = [];

                // Traiter chaque article
                foreach ($request->items as $item) {
                    if ($item['type'] === 'product') {
                        // Article en stock
                        $product = Product::where('id', $item['product_id'])
                                         ->where('tenant_id', $user->tenant_id)
                                         ->first();

                        if (!$product) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => "Produit {$item['product_id']} non trouvé"
                            ], 404);
                        }

                        if ($product->quantity < $item['quantity']) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => "Stock insuffisant pour {$product->name}. Stock disponible: {$product->quantity}"
                            ], 400);
                        }

                        $saleItems[] = [
                            'product_id' => $product->id,
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'description' => $product->name,
                            'details' => $item['details'] ?? null,
                            'product' => $product
                        ];
                    } else {
                        // Article personnalisé
                        $saleItems[] = [
                            'product_id' => null,
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'description' => $item['description'],
                            'details' => $item['details'] ?? null,
                            'product' => null
                        ];

                        // Enregistrer la marque de lentilles si c'est une lentille de contact
                        if (isset($item['itemType']) && $item['itemType'] === 'contact_lenses' &&
                            isset($item['details']['brand']) && !empty($item['details']['brand'])) {
                            ContactLensBrand::findOrCreate($user->tenant_id, $item['details']['brand']);
                        }
                    }

                    $totalAmount += $item['price'] * $item['quantity'];
                }

                // Créer la vente
                $sale = Sale::create([
                    'type' => Sale::TYPE_VENTE_DIRECTE,
                    'total_amount' => $totalAmount,
                    'client_id' => $client ? $client->id : null, // Peut être null pour vente anonyme
                    'user_id' => $user->id,
                    'tenant_id' => $user->tenant_id,
                    'status' => $request->paid_amount >= $totalAmount ? Sale::STATUS_LIVRE : Sale::STATUS_DEVIS,
                    'paid_amount' => $request->paid_amount,
                ]);

                // Créer les items de vente
                foreach ($saleItems as $saleItem) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $saleItem['product_id'],
                        'quantity' => $saleItem['quantity'],
                        'price' => $saleItem['price'],
                        'description' => $saleItem['description'],
                        'details' => $saleItem['details'],
                    ]);

                    // Décrémenter le stock si c'est un produit
                    if ($saleItem['product']) {
                        $saleItem['product']->decrement('quantity', $saleItem['quantity']);
                    }
                }

                DB::commit();

                // Recharger la vente avec ses relations
                $sale->load(['client', 'saleItems.product', 'user']);

                return response()->json([
                    'success' => true,
                    'message' => 'Vente créée avec succès',
                    'data' => $sale
                ], 201);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la vente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour le statut d'une vente avec historique
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user->tenant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non associé à un magasin'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:devis,en_commande,pret_pour_livraison,livre,annule',
                'comment' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $sale = Sale::where('id', $id)
                       ->where('tenant_id', $user->tenant_id)
                       ->first();

            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vente non trouvée'
                ], 404);
            }

            $oldStatus = $sale->status;
            $newStatus = $request->status;

            // Vérifier si le statut change réellement
            if ($oldStatus === $newStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le statut est déjà défini sur cette valeur'
                ], 400);
            }

            // Utiliser une transaction pour assurer la cohérence
            DB::beginTransaction();

            try {
                // Mettre à jour le statut de la vente
                $sale->status = $newStatus;
                $sale->save();

                // Créer l'entrée d'historique
                SaleStatusHistory::create([
                    'sale_id' => $sale->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'comment' => $request->comment,
                    'user_id' => $user->id,
                    'changed_at' => now()
                ]);

                DB::commit();

                // Recharger la vente avec ses relations
                $sale->load([
                    'client',
                    'user',
                    'saleItems.product.productCategory',
                    'statusHistories.user'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Statut mis à jour avec succès',
                    'data' => $sale
                ], 200);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
