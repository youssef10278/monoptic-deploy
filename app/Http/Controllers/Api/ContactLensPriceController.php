<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactLensPrice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContactLensPriceController extends Controller
{
    /**
     * Calculer le prix d'une lentille de contact
     */
    public function calculatePrice(Request $request): JsonResponse
    {
        try {
            // Debug: vérifier l'authentification
            \Log::info('ContactLensPrice API called', [
                'user' => auth()->user() ? auth()->user()->id : 'not authenticated',
                'tenant' => auth()->user() ? auth()->user()->tenant_id : 'no tenant',
                'request_data' => $request->all()
            ]);
            $user = auth()->user();

            // Validation des paramètres
            $request->validate([
                'lens_type' => 'required|string',
                'duration' => 'required|string',
                'box_size' => 'required|string',
                'brand' => 'nullable|string',
                'parameters' => 'nullable|array',
                'parameters.sphere' => 'nullable|numeric',
                'parameters.cylinder' => 'nullable|numeric',
                'parameters.addition' => 'nullable|numeric',
            ]);

            // Rechercher le prix correspondant
            $priceQuery = ContactLensPrice::forTenant($user->tenant_id)
                ->active()
                ->where('lens_type', $request->lens_type)
                ->where('duration', $request->duration)
                ->where('box_size', $request->box_size);

            // Si une marque spécifique est demandée, la prioriser
            if ($request->brand) {
                $specificPrice = clone $priceQuery;
                $specificPrice = $specificPrice->where('brand', $request->brand)->first();

                if ($specificPrice) {
                    $price = $specificPrice->calculatePrice($request->parameters ?? []);

                    return response()->json([
                        'success' => true,
                        'data' => [
                            'base_price' => $specificPrice->base_price,
                            'calculated_price' => $price,
                            'price_rule' => $specificPrice,
                            'parameters_applied' => $request->parameters ?? []
                        ]
                    ]);
                }
            }

            // Sinon, chercher un prix générique (sans marque spécifique)
            $genericPrice = $priceQuery->whereNull('brand')->first();

            if ($genericPrice) {
                $price = $genericPrice->calculatePrice($request->parameters ?? []);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'base_price' => $genericPrice->base_price,
                        'calculated_price' => $price,
                        'price_rule' => $genericPrice,
                        'parameters_applied' => $request->parameters ?? []
                    ]
                ]);
            }

            // Aucun prix trouvé
            return response()->json([
                'success' => false,
                'message' => 'Aucun prix configuré pour ces paramètres',
                'data' => [
                    'suggested_price' => $this->getSuggestedPrice($request->lens_type, $request->duration),
                    'parameters_applied' => $request->parameters ?? []
                ]
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul du prix',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prix suggéré par défaut si aucune règle n'est configurée
     */
    private function getSuggestedPrice($lensType, $duration)
    {
        $basePrices = [
            'spherique' => [
                'journaliere' => 25.00,
                'hebdomadaire' => 20.00,
                'mensuelle' => 30.00,
                'trimestrielle' => 45.00,
                'annuelle' => 60.00,
            ],
            'torique' => [
                'journaliere' => 35.00,
                'hebdomadaire' => 30.00,
                'mensuelle' => 40.00,
                'trimestrielle' => 55.00,
                'annuelle' => 70.00,
            ],
            'multifocale' => [
                'journaliere' => 45.00,
                'hebdomadaire' => 40.00,
                'mensuelle' => 50.00,
                'trimestrielle' => 65.00,
                'annuelle' => 80.00,
            ],
        ];

        return $basePrices[$lensType][$duration] ?? 30.00;
    }
}
