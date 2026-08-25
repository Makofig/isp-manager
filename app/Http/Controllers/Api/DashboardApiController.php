<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payments;
use App\Models\Proveedor;
use App\Models\Gasto;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function index(Request $request)
    {
        $anio = $request->get('anio', now()->year);
        $mes = $request->get('mes', now()->month);

        // Basic stats
        $totalClients = Client::count();
        $activeClients = Client::where('is_banned', false)->count();
        $bannedClients = Client::where('is_banned', true)->count();

        // Revenue
        $totalRevenue = Payments::where('estado', 1)->sum('abonado');
        $monthRevenue = Payments::where('estado', 1)
            ->whereYear('fecha_pago', $anio)
            ->whereMonth('fecha_pago', $mes)
            ->sum('abonado');

        // Costs
        $providerCosts = Proveedor::activos()->sum('precio_total');
        $monthExpenses = Gasto::whereYear('fecha_gasto', $anio)
            ->whereMonth('fecha_gasto', $mes)
            ->sum('monto');

        // Profitability
        $totalCosts = $providerCosts + $monthExpenses;
        $netProfit = $monthRevenue - $totalCosts;
        $margin = $monthRevenue > 0 ? ($netProfit / $monthRevenue) * 100 : 0;

        // Pending payments
        $pendingPayments = Payments::where('estado', 0)->count();
        $pendingAmount = Payments::where('estado', 0)->sum('costo');

        return response()->json([
            'clients' => [
                'total' => $totalClients,
                'active' => $activeClients,
                'banned' => $bannedClients,
            ],
            'financial' => [
                'total_revenue' => round($totalRevenue, 2),
                'month_revenue' => round($monthRevenue, 2),
                'provider_costs' => round($providerCosts, 2),
                'month_expenses' => round($monthExpenses, 2),
                'total_costs' => round($totalCosts, 2),
                'net_profit' => round($netProfit, 2),
                'margin_percent' => round($margin, 2),
                'is_profitable' => $netProfit > 0,
            ],
            'payments' => [
                'pending_count' => $pendingPayments,
                'pending_amount' => round($pendingAmount, 2),
            ],
            'period' => [
                'year' => (int) $anio,
                'month' => (int) $mes,
            ],
        ]);
    }
}
