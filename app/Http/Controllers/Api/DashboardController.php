<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Tableau de bord du tenant
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user->tenant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non associé à un magasin'
                ], 403);
            }

            $tenantId = $user->tenant_id;

            // Récupérer les paramètres de filtrage par dates
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            $period = $request->get('period', 'month'); // today, week, month, custom

            // Définir les dates selon la période
            switch ($period) {
                case 'today':
                    $dateFrom = Carbon::today()->startOfDay();
                    $dateTo = Carbon::today()->endOfDay();
                    break;
                case 'week':
                    $dateFrom = Carbon::now()->startOfWeek();
                    $dateTo = Carbon::now()->endOfWeek();
                    break;
                case 'month':
                    $dateFrom = Carbon::now()->startOfMonth();
                    $dateTo = Carbon::now()->endOfMonth();
                    break;
                case 'custom':
                    if ($dateFrom) {
                        $dateFrom = Carbon::parse($dateFrom)->startOfDay();
                    } else {
                        $dateFrom = Carbon::now()->startOfMonth();
                    }
                    if ($dateTo) {
                        $dateTo = Carbon::parse($dateTo)->endOfDay();
                    } else {
                        $dateTo = Carbon::now()->endOfMonth();
                    }
                    break;
                default:
                    $dateFrom = Carbon::now()->startOfMonth();
                    $dateTo = Carbon::now()->endOfMonth();
            }

            // Statistiques générales (toujours globales)
            $clientsCount = Client::where('tenant_id', $tenantId)->count();
            $productsCount = Product::where('tenant_id', $tenantId)->count();

            // Statistiques filtrées par période (excluant les devis et annulés)
            $confirmedStatuses = ['en_commande', 'pret_pour_livraison', 'livre'];

            $salesCount = Sale::where('tenant_id', $tenantId)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->whereIn('status', $confirmedStatuses)
                ->count();
            $totalRevenue = Sale::where('tenant_id', $tenantId)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->whereIn('status', $confirmedStatuses)
                ->sum('total_amount');

            // Statistiques par période (toujours calculées pour comparaison)
            $today = Carbon::today();
            $thisWeek = Carbon::now()->startOfWeek();
            $thisMonth = Carbon::now()->startOfMonth();

            // Ventes d'aujourd'hui (confirmées seulement)
            $todaySales = Sale::where('tenant_id', $tenantId)
                ->whereDate('created_at', $today)
                ->whereIn('status', $confirmedStatuses)
                ->count();
            $todayRevenue = Sale::where('tenant_id', $tenantId)
                ->whereDate('created_at', $today)
                ->whereIn('status', $confirmedStatuses)
                ->sum('total_amount');

            // Ventes de cette semaine (confirmées seulement)
            $weekSales = Sale::where('tenant_id', $tenantId)
                ->where('created_at', '>=', $thisWeek)
                ->whereIn('status', $confirmedStatuses)
                ->count();
            $weekRevenue = Sale::where('tenant_id', $tenantId)
                ->where('created_at', '>=', $thisWeek)
                ->whereIn('status', $confirmedStatuses)
                ->sum('total_amount');

            // Ventes de ce mois (confirmées seulement)
            $monthSales = Sale::where('tenant_id', $tenantId)
                ->where('created_at', '>=', $thisMonth)
                ->whereIn('status', $confirmedStatuses)
                ->count();
            $monthRevenue = Sale::where('tenant_id', $tenantId)
                ->where('created_at', '>=', $thisMonth)
                ->whereIn('status', $confirmedStatuses)
                ->sum('total_amount');

            // Stock
            $lowStockProducts = Product::where('tenant_id', $tenantId)
                ->where('quantity', '<=', 5)
                ->where('quantity', '>', 0)
                ->count();
            $outOfStockProducts = Product::where('tenant_id', $tenantId)
                ->where('quantity', 0)
                ->count();

            // Clients à crédit (calculé à partir des ventes non payées dans la période)
            $clientsWithCredit = Sale::where('tenant_id', $tenantId)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->whereColumn('paid_amount', '<', 'total_amount')
                ->whereNotNull('client_id')
                ->distinct('client_id')
                ->count();
            $totalCreditAmount = Sale::where('tenant_id', $tenantId)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->whereColumn('paid_amount', '<', 'total_amount')
                ->sum(DB::raw('total_amount - paid_amount'));

            // Ventes récentes (5 dernières dans la période)
            $recentSales = Sale::where('tenant_id', $tenantId)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->with(['client', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($sale) {
                    return [
                        'id' => $sale->id,
                        'total_amount' => $sale->total_amount,
                        'client_name' => $sale->client ? $sale->client->name : 'Client anonyme',
                        'user_name' => $sale->user->name,
                        'status' => $sale->status,
                        'created_at' => $sale->created_at->format('d/m/Y H:i'),
                    ];
                });

            // Évolution des ventes (adaptée à la période sélectionnée)
            $salesEvolution = $this->getSalesEvolution($tenantId, $dateFrom, $dateTo, $period);

            return response()->json([
                'success' => true,
                'data' => [
                    'period_info' => [
                        'period' => $period,
                        'date_from' => $dateFrom->format('Y-m-d'),
                        'date_to' => $dateTo->format('Y-m-d'),
                        'label' => $this->getPeriodLabel($period, $dateFrom, $dateTo),
                    ],
                    'stats' => [
                        'clients' => $clientsCount,
                        'products' => $productsCount,
                        'sales' => $salesCount,
                        'revenue' => $totalRevenue,
                    ],
                    'today' => [
                        'sales' => $todaySales,
                        'revenue' => $todayRevenue,
                    ],
                    'week' => [
                        'sales' => $weekSales,
                        'revenue' => $weekRevenue,
                    ],
                    'month' => [
                        'sales' => $monthSales,
                        'revenue' => $monthRevenue,
                    ],
                    'stock' => [
                        'low_stock' => $lowStockProducts,
                        'out_of_stock' => $outOfStockProducts,
                    ],
                    'credits' => [
                        'clients_count' => $clientsWithCredit,
                        'total_amount' => $totalCreditAmount,
                    ],
                    'recent_sales' => $recentSales,
                    'sales_evolution' => $salesEvolution,
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

    /**
     * Générer l'évolution des ventes selon la période
     */
    private function getSalesEvolution($tenantId, $dateFrom, $dateTo, $period)
    {
        $salesEvolution = [];
        $diffInDays = $dateFrom->diffInDays($dateTo);
        $confirmedStatuses = ['en_commande', 'pret_pour_livraison', 'livre'];

        if ($period === 'today' || $diffInDays <= 1) {
            // Évolution par heures pour aujourd'hui
            for ($i = 0; $i < 24; $i++) {
                $hourStart = $dateFrom->copy()->addHours($i);
                $hourEnd = $hourStart->copy()->addHour();

                $hourlySales = Sale::where('tenant_id', $tenantId)
                    ->whereBetween('created_at', [$hourStart, $hourEnd])
                    ->whereIn('status', $confirmedStatuses)
                    ->count();
                $hourlyRevenue = Sale::where('tenant_id', $tenantId)
                    ->whereBetween('created_at', [$hourStart, $hourEnd])
                    ->whereIn('status', $confirmedStatuses)
                    ->sum('total_amount');

                $salesEvolution[] = [
                    'date' => $hourStart->format('H:i'),
                    'sales' => $hourlySales,
                    'revenue' => $hourlyRevenue,
                ];
            }
        } elseif ($diffInDays <= 31) {
            // Évolution par jours
            $currentDate = $dateFrom->copy();
            while ($currentDate <= $dateTo) {
                $dailySales = Sale::where('tenant_id', $tenantId)
                    ->whereDate('created_at', $currentDate)
                    ->whereIn('status', $confirmedStatuses)
                    ->count();
                $dailyRevenue = Sale::where('tenant_id', $tenantId)
                    ->whereDate('created_at', $currentDate)
                    ->whereIn('status', $confirmedStatuses)
                    ->sum('total_amount');

                $salesEvolution[] = [
                    'date' => $currentDate->format('d/m'),
                    'sales' => $dailySales,
                    'revenue' => $dailyRevenue,
                ];

                $currentDate->addDay();
            }
        } else {
            // Évolution par semaines pour les longues périodes
            $currentDate = $dateFrom->copy()->startOfWeek();
            while ($currentDate <= $dateTo) {
                $weekEnd = $currentDate->copy()->endOfWeek();
                if ($weekEnd > $dateTo) {
                    $weekEnd = $dateTo;
                }

                $weeklySales = Sale::where('tenant_id', $tenantId)
                    ->whereBetween('created_at', [$currentDate, $weekEnd])
                    ->whereIn('status', $confirmedStatuses)
                    ->count();
                $weeklyRevenue = Sale::where('tenant_id', $tenantId)
                    ->whereBetween('created_at', [$currentDate, $weekEnd])
                    ->whereIn('status', $confirmedStatuses)
                    ->sum('total_amount');

                $salesEvolution[] = [
                    'date' => $currentDate->format('d/m'),
                    'sales' => $weeklySales,
                    'revenue' => $weeklyRevenue,
                ];

                $currentDate->addWeek();
            }
        }

        return $salesEvolution;
    }

    /**
     * Générer le libellé de la période
     */
    private function getPeriodLabel($period, $dateFrom, $dateTo)
    {
        switch ($period) {
            case 'today':
                return "Aujourd'hui (" . $dateFrom->format('d/m/Y') . ")";
            case 'week':
                return "Cette semaine (" . $dateFrom->format('d/m') . " - " . $dateTo->format('d/m/Y') . ")";
            case 'month':
                return "Ce mois (" . $dateFrom->format('M Y') . ")";
            case 'custom':
                return "Période personnalisée (" . $dateFrom->format('d/m/Y') . " - " . $dateTo->format('d/m/Y') . ")";
            default:
                return "Période sélectionnée";
        }
    }
}
