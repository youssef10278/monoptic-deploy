<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Sale;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Tableau de bord du Super Administrateur
     */
    public function index(): JsonResponse
    {
        try {
            $user = auth()->user();

            // Vérifier que l'utilisateur est bien un Super Admin
            if ($user->role->value !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès refusé. Seuls les Super Administrateurs peuvent accéder à cette ressource.'
                ], 403);
            }

            // Récupérer tous les tenants avec leurs informations
            $tenants = Tenant::withCount(['users', 'clients', 'products', 'sales'])
                ->get()
                ->map(function ($tenant) {
                    // Calculer le CA total du tenant
                    $totalRevenue = Sale::where('tenant_id', $tenant->id)->sum('total_amount');

                    // Calculer le CA du mois en cours
                    $monthlyRevenue = Sale::where('tenant_id', $tenant->id)
                        ->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->sum('total_amount');

                    $tenant->total_revenue = $totalRevenue;
                    $tenant->monthly_revenue = $monthlyRevenue;
                    $tenant->is_active = $tenant->status === 'active' &&
                                       ($tenant->subscription_end_date === null ||
                                        Carbon::parse($tenant->subscription_end_date)->isFuture());

                    return $tenant;
                });

            // Calculer les KPIs globaux
            $totalTenants = $tenants->count();
            $activeTenants = $tenants->where('is_active', true)->count();
            $trialTenants = $tenants->where('status', 'trial')->count();
            $expiredTenants = $tenants->filter(function ($tenant) {
                return $tenant->subscription_end_date &&
                       Carbon::parse($tenant->subscription_end_date)->isPast() &&
                       $tenant->status !== 'trial';
            })->count();

            // Calculer le MRR (Monthly Recurring Revenue)
            // Prix fixe de 300€ par tenant actif
            $subscriptionPrice = 300;
            $mrr = $activeTenants * $subscriptionPrice;

            // Calculer le revenu total de tous les tenants
            $totalBusinessRevenue = $tenants->sum('total_revenue');
            $monthlyBusinessRevenue = $tenants->sum('monthly_revenue');

            // Statistiques d'utilisation globales
            $totalUsers = User::where('role', '!=', 'SuperAdmin')->count();
            $totalClients = Client::count();
            $totalProducts = Product::count();
            $totalSales = Sale::count();

            // Évolution des tenants sur les 12 derniers mois
            $tenantGrowth = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $count = Tenant::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $tenantGrowth[] = [
                    'month' => $date->format('M Y'),
                    'count' => $count
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'kpis' => [
                        'total_tenants' => $totalTenants,
                        'active_tenants' => $activeTenants,
                        'trial_tenants' => $trialTenants,
                        'expired_tenants' => $expiredTenants,
                        'mrr' => $mrr,
                        'total_business_revenue' => $totalBusinessRevenue,
                        'monthly_business_revenue' => $monthlyBusinessRevenue,
                        'total_users' => $totalUsers,
                        'total_clients' => $totalClients,
                        'total_products' => $totalProducts,
                        'total_sales' => $totalSales,
                    ],
                    'tenants' => $tenants,
                    'tenant_growth' => $tenantGrowth,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des données du tableau de bord',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
