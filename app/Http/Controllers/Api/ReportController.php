<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Rapport des ventes avec chiffre d'affaires et évolution
     */
    public function salesReport(Request $request): JsonResponse
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

            // Récupérer les paramètres de filtrage par dates
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            $period = $request->get('period', 'month'); // today, week, month, custom

            // Définir les dates selon la période
            switch ($period) {
                case 'today':
                    $startDate = Carbon::today()->startOfDay();
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
                case 'custom':
                    if ($dateFrom) {
                        $startDate = Carbon::parse($dateFrom)->startOfDay();
                    } else {
                        $startDate = Carbon::now()->startOfMonth();
                    }
                    if ($dateTo) {
                        $endDate = Carbon::parse($dateTo)->endOfDay();
                    } else {
                        $endDate = Carbon::now()->endOfMonth();
                    }
                    break;
                default:
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
            }

            // Requête de base pour les ventes du tenant (excluant les devis et annulés)
            $confirmedStatuses = ['en_commande', 'pret_pour_livraison', 'livre'];

            $salesQuery = Sale::where('tenant_id', $user->tenant_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', $confirmedStatuses);

            // Statistiques globales (seulement les ventes confirmées)
            $totalRevenue = $salesQuery->sum('total_amount');
            $totalSales = $salesQuery->count();
            $averageSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

            // Évolution par période adaptée aux nouveaux filtres
            try {
                $salesEvolution = $this->getSalesEvolutionForReports($user->tenant_id, $startDate, $endDate, $period);
            } catch (\Exception $e) {
                // En cas d'erreur, retourner un tableau vide
                $salesEvolution = [];
            }

            // Statistiques par période (aujourd'hui, cette semaine, ce mois)
            $today = Carbon::today();
            $thisWeek = Carbon::now()->startOfWeek();
            $thisMonth = Carbon::now()->startOfMonth();

            $todayRevenue = Sale::where('tenant_id', $user->tenant_id)
                ->whereDate('created_at', $today)
                ->whereIn('status', $confirmedStatuses)
                ->sum('total_amount');

            $weekRevenue = Sale::where('tenant_id', $user->tenant_id)
                ->where('created_at', '>=', $thisWeek)
                ->whereIn('status', $confirmedStatuses)
                ->sum('total_amount');

            $monthRevenue = Sale::where('tenant_id', $user->tenant_id)
                ->where('created_at', '>=', $thisMonth)
                ->whereIn('status', $confirmedStatuses)
                ->sum('total_amount');

            // Statistiques de comparaison (période précédente)
            $previousPeriodStart = null;
            $previousPeriodEnd = null;

            if ($period === 'day') {
                $previousPeriodStart = $startDate->copy()->subDay();
                $previousPeriodEnd = $endDate->copy()->subDay();
            } elseif ($period === 'week') {
                $previousPeriodStart = $startDate->copy()->subWeek();
                $previousPeriodEnd = $endDate->copy()->subWeek();
            } elseif ($period === 'month') {
                $previousPeriodStart = $startDate->copy()->subMonth();
                $previousPeriodEnd = $endDate->copy()->subMonth();
            } elseif ($period === 'year') {
                $previousPeriodStart = $startDate->copy()->subYear();
                $previousPeriodEnd = $endDate->copy()->subYear();
            } else {
                // Pour les périodes personnalisées, calculer la même durée avant
                $daysDiff = $startDate->diffInDays($endDate);
                $previousPeriodEnd = $startDate->copy()->subDay();
                $previousPeriodStart = $previousPeriodEnd->copy()->subDays($daysDiff);
            }

            $previousRevenue = Sale::where('tenant_id', $user->tenant_id)
                ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
                ->whereIn('status', $confirmedStatuses)
                ->sum('total_amount');

            $previousSales = Sale::where('tenant_id', $user->tenant_id)
                ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
                ->whereIn('status', $confirmedStatuses)
                ->count();

            $previousAverageSale = $previousSales > 0 ? $previousRevenue / $previousSales : 0;

            // Calcul des pourcentages d'évolution
            $revenueGrowth = $previousRevenue > 0 ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;
            $salesGrowth = $previousSales > 0 ? (($totalSales - $previousSales) / $previousSales) * 100 : 0;
            $averageSaleGrowth = $previousAverageSale > 0 ? (($averageSale - $previousAverageSale) / $previousAverageSale) * 100 : 0;

            // Statistiques comparatives pour aujourd'hui, semaine, mois
            $yesterdayRevenue = Sale::where('tenant_id', $user->tenant_id)
                ->whereDate('created_at', Carbon::yesterday())
                ->whereIn('status', $confirmedStatuses)
                ->sum('total_amount');

            $lastWeekRevenue = Sale::where('tenant_id', $user->tenant_id)
                ->whereBetween('created_at', [
                    Carbon::now()->subWeek()->startOfWeek(),
                    Carbon::now()->subWeek()->endOfWeek()
                ])
                ->whereIn('status', $confirmedStatuses)
                ->sum('total_amount');

            $lastMonthRevenue = Sale::where('tenant_id', $user->tenant_id)
                ->whereBetween('created_at', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth()
                ])
                ->whereIn('status', $confirmedStatuses)
                ->sum('total_amount');

            // Calcul des croissances pour les périodes rapides
            $todayGrowth = $yesterdayRevenue > 0 ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 : 0;
            $weekGrowth = $lastWeekRevenue > 0 ? (($weekRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100 : 0;
            $monthGrowth = $lastMonthRevenue > 0 ? (($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'period_info' => [
                        'period' => $period,
                        'date_from' => $startDate->format('Y-m-d'),
                        'date_to' => $endDate->format('Y-m-d'),
                        'label' => $this->getPeriodLabelForReports($period, $startDate, $endDate),
                    ],
                    'summary' => [
                        'total_revenue' => $totalRevenue,
                        'total_sales' => $totalSales,
                        'average_sale' => $averageSale,
                        'today_revenue' => $todayRevenue,
                        'week_revenue' => $weekRevenue,
                        'month_revenue' => $monthRevenue,
                        // Données de comparaison
                        'previous_revenue' => $previousRevenue,
                        'previous_sales' => $previousSales,
                        'previous_average_sale' => $previousAverageSale,
                        'yesterday_revenue' => $yesterdayRevenue,
                        'last_week_revenue' => $lastWeekRevenue,
                        'last_month_revenue' => $lastMonthRevenue,
                        // Pourcentages d'évolution
                        'revenue_growth' => round($revenueGrowth, 2),
                        'sales_growth' => round($salesGrowth, 2),
                        'average_sale_growth' => round($averageSaleGrowth, 2),
                        'today_growth' => round($todayGrowth, 2),
                        'week_growth' => round($weekGrowth, 2),
                        'month_growth' => round($monthGrowth, 2),
                    ],
                    'evolution' => $salesEvolution,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du rapport des ventes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test simple pour diagnostiquer les erreurs
     */
    public function testProductAnalysis(): JsonResponse
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

            // Test simple : compter les produits
            $productCount = Product::where('tenant_id', $user->tenant_id)->count();

            // Test simple : compter les ventes
            $salesCount = Sale::where('tenant_id', $user->tenant_id)->count();

            // Test simple : compter les sale_items
            $saleItemsCount = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->where('sales.tenant_id', $user->tenant_id)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'user_tenant_id' => $user->tenant_id,
                    'product_count' => $productCount,
                    'sales_count' => $salesCount,
                    'sale_items_count' => $saleItemsCount,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du test',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Analyse des produits : top ventes et top rentabilité
     */
    public function productAnalysis(): JsonResponse
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

            // Statuts de ventes confirmées
            $confirmedStatuses = ['en_commande', 'pret_pour_livraison', 'livre'];

            // Version simplifiée pour tester
            $topSellingProducts = collect([]);

            // Test étape par étape
            try {
                // Étape 1: Vérifier s'il y a des produits
                $hasProducts = Product::where('tenant_id', $user->tenant_id)->exists();
                if (!$hasProducts) {
                    throw new \Exception("Aucun produit trouvé pour ce tenant");
                }

                // Étape 2: Vérifier s'il y a des ventes
                $hasSales = Sale::where('tenant_id', $user->tenant_id)->exists();
                if (!$hasSales) {
                    throw new \Exception("Aucune vente trouvée pour ce tenant");
                }

                // Étape 3: Vérifier s'il y a des sale_items
                $hasSaleItems = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.tenant_id', $user->tenant_id)
                    ->exists();
                if (!$hasSaleItems) {
                    throw new \Exception("Aucun sale_item trouvé pour ce tenant");
                }

                // Étape 4: Requête simple sans GROUP BY
                $topSellingProducts = Product::where('products.tenant_id', $user->tenant_id)
                    ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.tenant_id', $user->tenant_id)
                    ->whereIn('sales.status', $confirmedStatuses)
                    ->select('products.*')
                    ->limit(5)
                    ->get();

            } catch (\Exception $e) {
                throw new \Exception("Erreur dans topSellingProducts: " . $e->getMessage());
            }

            // Version simplifiée pour les produits rentables
            $topProfitableProducts = collect([]);

            // Calcul des statistiques globales
            $totalProductsSold = $topSellingProducts->sum('total_sold');
            $totalRevenueFromTopProducts = $topSellingProducts->sum('total_revenue');
            $totalProfitFromTopProducts = $topProfitableProducts->sum('total_profit');

            // Produits avec alertes de stock faible
            $lowStockProducts = Product::where('tenant_id', $user->tenant_id)
                ->where('quantity', '<=', 5)
                ->where('quantity', '>', 0)
                ->with('productCategory')
                ->orderBy('quantity', 'asc')
                ->limit(5)
                ->get();

            // Produits sans stock
            $outOfStockProducts = Product::where('tenant_id', $user->tenant_id)
                ->where('quantity', '<=', 0)
                ->with('productCategory')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'top_selling' => $topSellingProducts,
                    'top_profitable' => $topProfitableProducts,
                    'low_stock_products' => $lowStockProducts,
                    'out_of_stock_products' => $outOfStockProducts,
                    'summary' => [
                        'total_products_sold' => $totalProductsSold,
                        'total_revenue_top_selling' => $totalRevenueFromTopProducts,
                        'total_profit_top_profitable' => $totalProfitFromTopProducts,
                        'low_stock_count' => $lowStockProducts->count(),
                        'out_of_stock_count' => $outOfStockProducts->count(),
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse des produits',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Valeur du stock (prix d'achat et prix de vente)
     */
    public function stockValue(): JsonResponse
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

            // Calculer la valeur du stock au prix d'achat
            $purchaseValue = Product::where('tenant_id', $user->tenant_id)
                ->whereNotNull('purchase_price')
                ->selectRaw('SUM(quantity * purchase_price) as total')
                ->value('total') ?? 0;

            // Calculer la valeur du stock au prix de vente
            $sellingValue = Product::where('tenant_id', $user->tenant_id)
                ->selectRaw('SUM(quantity * selling_price) as total')
                ->value('total') ?? 0;

            // Calculer la marge potentielle
            $potentialProfit = $sellingValue - $purchaseValue;

            // Statistiques supplémentaires
            $totalProducts = Product::where('tenant_id', $user->tenant_id)->count();
            $totalQuantity = Product::where('tenant_id', $user->tenant_id)->sum('quantity');
            $lowStockProducts = Product::where('tenant_id', $user->tenant_id)
                ->where('quantity', '<=', 5)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'purchase_value' => $purchaseValue,
                    'selling_value' => $sellingValue,
                    'potential_profit' => $potentialProfit,
                    'total_products' => $totalProducts,
                    'total_quantity' => $totalQuantity,
                    'low_stock_products' => $lowStockProducts,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul de la valeur du stock',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Générer l'évolution des ventes pour les rapports selon la période
     */
    private function getSalesEvolutionForReports($tenantId, $startDate, $endDate, $period)
    {
        $diffInDays = $startDate->diffInDays($endDate);
        $confirmedStatuses = ['en_commande', 'pret_pour_livraison', 'livre'];

        if ($period === 'today' || $diffInDays <= 1) {
            // Évolution par heures pour aujourd'hui
            return Sale::where('tenant_id', $tenantId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', $confirmedStatuses)
                ->select(
                    DB::raw('HOUR(created_at) as hour'),
                    DB::raw('SUM(total_amount) as revenue'),
                    DB::raw('COUNT(*) as sales_count')
                )
                ->groupBy('hour')
                ->orderBy('hour')
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => sprintf('%02d:00', $item->hour),
                        'revenue' => $item->revenue,
                        'sales_count' => $item->sales_count
                    ];
                });
        } elseif ($diffInDays <= 31) {
            // Évolution par jours
            return Sale::where('tenant_id', $tenantId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', $confirmedStatuses)
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total_amount) as revenue'),
                    DB::raw('COUNT(*) as sales_count')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => Carbon::parse($item->date)->format('d/m'),
                        'revenue' => $item->revenue,
                        'sales_count' => $item->sales_count
                    ];
                });
        } else {
            // Évolution par semaines pour les longues périodes
            return Sale::where('tenant_id', $tenantId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', $confirmedStatuses)
                ->select(
                    DB::raw('YEARWEEK(created_at) as week'),
                    DB::raw('MIN(DATE(created_at)) as week_start'),
                    DB::raw('SUM(total_amount) as revenue'),
                    DB::raw('COUNT(*) as sales_count')
                )
                ->groupBy('week')
                ->orderBy('week')
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => Carbon::parse($item->week_start)->format('d/m'),
                        'revenue' => $item->revenue,
                        'sales_count' => $item->sales_count
                    ];
                });
        }
    }

    /**
     * Générer le libellé de la période pour les rapports
     */
    private function getPeriodLabelForReports($period, $startDate, $endDate)
    {
        switch ($period) {
            case 'today':
                return "Aujourd'hui (" . $startDate->format('d/m/Y') . ")";
            case 'week':
                return "Cette semaine (" . $startDate->format('d/m') . " - " . $endDate->format('d/m/Y') . ")";
            case 'month':
                return "Ce mois (" . $startDate->format('M Y') . ")";
            case 'custom':
                return "Période personnalisée (" . $startDate->format('d/m/Y') . " - " . $endDate->format('d/m/Y') . ")";
            default:
                return "Période sélectionnée";
        }
    }

    /**
     * Analyse des top clients par chiffre d'affaires
     */
    public function topClientsAnalysis(): JsonResponse
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

            // Statuts de ventes confirmées
            $confirmedStatuses = ['en_commande', 'pret_pour_livraison', 'livre'];

            // Top 5 clients par chiffre d'affaires
            $topClients = DB::table('clients')
                ->join('sales', 'clients.id', '=', 'sales.client_id')
                ->where('clients.tenant_id', $user->tenant_id)
                ->whereIn('sales.status', $confirmedStatuses)
                ->select(
                    'clients.id',
                    'clients.name',
                    'clients.email',
                    'clients.phone',
                    DB::raw('SUM(sales.total_amount) as total_revenue'),
                    DB::raw('COUNT(sales.id) as total_orders'),
                    DB::raw('AVG(sales.total_amount) as average_order'),
                    DB::raw('MAX(sales.created_at) as last_order_date'),
                    DB::raw('MIN(sales.created_at) as first_order_date')
                )
                ->groupBy('clients.id', 'clients.name', 'clients.email', 'clients.phone')
                ->orderBy('total_revenue', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($client) {
                    // Déterminer si c'est un client VIP (plus de 5 commandes ou CA > 10000)
                    $client->is_vip = $client->total_orders >= 5 || $client->total_revenue >= 10000;

                    // Calculer la fréquence d'achat
                    $firstOrder = Carbon::parse($client->first_order_date);
                    $lastOrder = Carbon::parse($client->last_order_date);
                    $daysBetween = $firstOrder->diffInDays($lastOrder);
                    $client->purchase_frequency = $daysBetween > 0 ? round($daysBetween / $client->total_orders, 1) : 0;

                    return $client;
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'top_clients' => $topClients,
                    'summary' => [
                        'total_clients_analyzed' => $topClients->count(),
                        'top5_total_revenue' => $topClients->sum('total_revenue'),
                        'top5_total_orders' => $topClients->sum('total_orders'),
                        'top5_average_order' => $topClients->count() > 0 ? $topClients->sum('total_revenue') / $topClients->sum('total_orders') : 0,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse des top clients',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
