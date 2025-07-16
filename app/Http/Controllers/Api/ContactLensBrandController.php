<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactLensBrand;
use Illuminate\Http\Request;

class ContactLensBrandController extends Controller
{
    /**
     * Recherche de marques avec autocomplétion
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $tenantId = auth()->user()->tenant_id;

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $brands = ContactLensBrand::searchBrands($tenantId, $query);

            return response()->json([
                'success' => true,
                'data' => $brands
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche des marques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les marques les plus utilisées
     */
    public function popular(Request $request)
    {
        try {
            // Debug: vérifier l'authentification
            \Log::info('ContactLensBrand popular API called', [
                'user' => auth()->user() ? auth()->user()->id : 'not authenticated',
                'tenant' => auth()->user() ? auth()->user()->tenant_id : 'no tenant'
            ]);
            $tenantId = auth()->user()->tenant_id;
            $limit = $request->get('limit', 10);

            $brands = ContactLensBrand::where('tenant_id', $tenantId)
                                    ->orderBy('usage_count', 'desc')
                                    ->orderBy('last_used_at', 'desc')
                                    ->limit($limit)
                                    ->pluck('name')
                                    ->toArray();

            return response()->json([
                'success' => true,
                'data' => $brands
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des marques populaires',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
