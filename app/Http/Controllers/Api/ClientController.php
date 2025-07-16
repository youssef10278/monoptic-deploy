<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Afficher la liste des clients du tenant de l'utilisateur authentifié
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

            $clients = Client::where('tenant_id', $user->tenant_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $clients
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des clients',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer un nouveau client
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
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:1000',
            ], [
                'name.required' => 'Le nom du client est requis',
                'email.email' => 'L\'email doit être valide',
                'phone.max' => 'Le numéro de téléphone ne doit pas dépasser 20 caractères',
                'address.max' => 'L\'adresse ne doit pas dépasser 1000 caractères',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Créer le client avec le tenant_id de l'utilisateur authentifié
            $client = Client::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'tenant_id' => $user->tenant_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Client créé avec succès',
                'data' => $client
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un client spécifique
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

            // Récupérer le client uniquement s'il appartient au tenant de l'utilisateur
            $client = Client::where('tenant_id', $user->tenant_id)
                ->where('id', $id)
                ->first();

            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $client
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher l'historique des ventes d'un client spécifique
     */
    public function sales($id): JsonResponse
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

            // Vérifier que le client appartient au tenant de l'utilisateur
            $client = Client::where('tenant_id', $user->tenant_id)
                ->where('id', $id)
                ->first();

            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client non trouvé'
                ], 404);
            }

            // Récupérer les ventes du client avec les détails
            $sales = $client->sales()
                ->with([
                    'saleItems.product',
                    'user',
                    'payments'
                ])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($sale) {
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

                    return $sale;
                });

            return response()->json([
                'success' => true,
                'data' => $sales
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des ventes du client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour un client
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

            // Récupérer le client uniquement s'il appartient au tenant de l'utilisateur
            $client = Client::where('tenant_id', $user->tenant_id)
                ->where('id', $id)
                ->first();

            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client non trouvé'
                ], 404);
            }

            // Validation des données
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:1000',
            ], [
                'name.required' => 'Le nom du client est requis',
                'email.email' => 'L\'email doit être valide',
                'phone.max' => 'Le numéro de téléphone ne doit pas dépasser 20 caractères',
                'address.max' => 'L\'adresse ne doit pas dépasser 1000 caractères',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Mettre à jour le client
            $client->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Client mis à jour avec succès',
                'data' => $client->fresh()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un client
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

            // Récupérer le client uniquement s'il appartient au tenant de l'utilisateur
            $client = Client::where('tenant_id', $user->tenant_id)
                ->where('id', $id)
                ->first();

            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client non trouvé'
                ], 404);
            }

            // Supprimer le client
            $client->delete();

            return response()->json([
                'success' => true,
                'message' => 'Client supprimé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher la liste des clients ayant un solde débiteur (à crédit)
     */
    public function credits(): JsonResponse
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

            // Récupérer les clients avec leurs ventes non entièrement payées
            $clientsWithCredits = Client::where('tenant_id', $user->tenant_id)
                ->whereHas('sales', function ($query) {
                    $query->where('status', '!=', 'paid')
                        ->orWhere('paid_amount', '<', DB::raw('total_amount'));
                })
                ->with(['sales' => function ($query) {
                    $query->where('status', '!=', 'paid')
                        ->orWhere('paid_amount', '<', DB::raw('total_amount'));
                }])
                ->get()
                ->map(function ($client) {
                    // Calculer le total dû pour ce client
                    $totalDue = $client->sales->sum(function ($sale) {
                        return $sale->total_amount - $sale->paid_amount;
                    });

                    // Ajouter les informations de crédit au client
                    $client->total_due = $totalDue;
                    $client->unpaid_sales_count = $client->sales->count();
                    $client->oldest_unpaid_sale = $client->sales->min('created_at');

                    return $client;
                })
                ->filter(function ($client) {
                    // Ne garder que les clients qui ont vraiment un solde débiteur
                    return $client->total_due > 0;
                })
                ->sortByDesc('total_due')
                ->values();

            return response()->json([
                'success' => true,
                'data' => $clientsWithCredits
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des clients à crédit',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
