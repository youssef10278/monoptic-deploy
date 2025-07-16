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
            $salesEvolution = $this->getSalesEvolutionForReports($user->tenant_id, $startDate, $endDate, $period);

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

            // Top 5 produits les plus vendus (par quantité)
            $topSellingProducts = Product::where('products.tenant_id', $user->tenant_id)
                ->select('products.*')
                ->selectRaw('SUM(sale_items.quantity) as total_sold')
                ->selectRaw('SUM(sale_items.quantity * sale_items.price) as total_revenue')
                ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->where('sales.tenant_id', $user->tenant_id)
                ->with('productCategory')
                ->groupBy('products.id')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();

            // Top 5 produits les plus rentables (par marge)
            $topProfitableProducts = Product::where('products.tenant_id', $user->tenant_id)
                ->select('products.*')
                ->selectRaw('SUM(sale_items.quantity) as total_sold')
                ->selectRaw('SUM(sale_items.quantity * (sale_items.price - COALESCE(products.purchase_price, 0))) as total_profit')
                ->selectRaw('SUM(sale_items.quantity * sale_items.price) as total_revenue')
                ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->where('sales.tenant_id', $user->tenant_id)
                ->whereNotNull('products.purchase_price')
                ->with('productCategory')
                ->groupBy('products.id')
                ->orderByDesc('total_profit')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'top_selling' => $topSellingProducts,
                    'top_profitable' => $topProfitableProducts,
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
}
