<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Enregistrer un nouveau paiement
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
                'sale_id' => 'required|exists:sales,id',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|string|in:cash,card,transfer,check,other',
            ], [
                'sale_id.required' => 'L\'ID de la vente est requis',
                'sale_id.exists' => 'La vente sélectionnée n\'existe pas',
                'amount.required' => 'Le montant est requis',
                'amount.numeric' => 'Le montant doit être un nombre',
                'amount.min' => 'Le montant doit être supérieur à 0',
                'payment_method.required' => 'La méthode de paiement est requise',
                'payment_method.in' => 'Méthode de paiement invalide',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Utiliser une transaction pour garantir la cohérence
            $payment = DB::transaction(function () use ($request, $user) {
                // Vérifier que la vente appartient au tenant
                $sale = Sale::where('id', $request->sale_id)
                    ->where('tenant_id', $user->tenant_id)
                    ->first();

                if (!$sale) {
                    throw new \Exception('Vente non trouvée ou non autorisée');
                }

                // Vérifier que le montant du paiement ne dépasse pas le montant restant
                $remainingAmount = $sale->total_amount - $sale->paid_amount;
                if ($request->amount > $remainingAmount) {
                    throw new \Exception("Le montant du paiement ({$request->amount} MAD) dépasse le montant restant à payer ({$remainingAmount} MAD)");
                }

                // Créer le paiement
                $payment = Payment::create([
                    'sale_id' => $request->sale_id,
                    'amount' => $request->amount,
                    'payment_method' => $request->payment_method,
                    'tenant_id' => $user->tenant_id,
                    'user_id' => $user->id,
                ]);

                // Mettre à jour le montant payé de la vente
                $sale->paid_amount += $request->amount;

                // Mettre à jour le statut de la vente
                $sale->updatePaymentStatus();

                return $payment;
            });

            // Charger les relations pour la réponse
            $payment->load(['sale.client', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Paiement enregistré avec succès',
                'data' => $payment
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement du paiement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher la liste des paiements du tenant
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

            $payments = Payment::where('tenant_id', $user->tenant_id)
                ->with(['sale.client', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $payments
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des paiements',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
