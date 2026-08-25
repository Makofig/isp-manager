<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Client;
use App\Models\Quota;
use App\Models\Payments;
use App\Models\Contracts;
use App\Models\Proveedor;
use App\Models\Gasto;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $totalClients;
    public $totalPayments;
    public $totalPaid;
    public $totalDebt;
    public $paymentsPerMonth;
    public $paymentStatuses;
    public $topDebtors;
    public $topPayers;
    public $totalMegabytesUsed;

    // Profitability metrics
    public $monthRevenue;
    public $providerCosts;
    public $monthExpenses;
    public $netProfit;
    public $marginPercent;
    public $isProfitable;

    protected $listeners = ['refreshDashboard' => 'refreshData'];

    public function refreshData()
    {
        cache()->forget('stats_global');
        $data = $this->cacheData();
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        $this->dispatch('refreshCharts', [
            'paymentsPerMonth' => $this->paymentsPerMonth,
            'paymentStatuses' => $this->paymentStatuses,
        ]);
    }

    public function cacheData() {
        return cache()->remember('stats_global', 300, function() {
            $data = [
                'totalClients' => Client::count(),
                'totalPayments' => Payments::count(),
                'totalPaid' => Payments::where('estado', '1')->sum('abonado'),
                'totalDebt' => Payments::where('estado', '0')->sum('costo'),
                'totalMegabytesUsed' => Client::join('plan', 'cliente.id_plan', '=', 'plan.id')
                    ->sum('plan.megabytes'),
                'paymentsPerMonth' => Payments::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(abonado) as totalPagado, SUM(costo) as totalCuotas')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get()
                    ->mapWithKeys(fn($row) => [
                        $row->month => [
                            'totalCuotas' => $row->totalCuotas,
                            'totalPagado' => $row->totalPagado,
                            'deuda' => $row->totalCuotas - $row->totalPagado,
                        ]
                    ])
                    ->toArray(),
                'paymentStatuses' => Payments::select('estado', DB::raw('count(*) as total'))
                    ->groupBy('estado')
                    ->pluck('total', 'estado')
                    ->toArray(),
                'topDebtors' => Client::withSum(['pagos as deuda' => function ($q) {
                    $q->where('estado', '0');
                }], 'costo')
                    ->orderByDesc('deuda')
                    ->take(5)
                    ->get()
                    ->toArray(),
                'topPayers' => Client::withSum(['pagos as total_paid' => function ($q) {
                        $q->where('estado', 1);
                    }], 'abonado')
                    ->orderByDesc('total_paid')
                    ->take(5)
                    ->get()
                    ->toArray(),
            ];

            // Profitability
            $data['monthRevenue'] = Payments::where('estado', 1)
                ->whereYear('fecha_pago', now()->year)
                ->whereMonth('fecha_pago', now()->month)
                ->sum('abonado');
            $data['providerCosts'] = Proveedor::activos()->sum('precio_total');
            $data['monthExpenses'] = Gasto::whereYear('fecha_gasto', now()->year)
                ->whereMonth('fecha_gasto', now()->month)
                ->sum('monto');
            $totalCosts = $data['providerCosts'] + $data['monthExpenses'];
            $data['netProfit'] = $data['monthRevenue'] - $totalCosts;
            $data['marginPercent'] = $data['monthRevenue'] > 0
                ? round(($data['netProfit'] / $data['monthRevenue']) * 100, 2)
                : 0;
            $data['isProfitable'] = $data['netProfit'] > 0;

            return $data;
        });
    }

    public function mount()
    {
        $data = $this->cacheData();

        foreach ($data as $key => $value) {
             $this->$key = $value;
        }

        $this->dispatch('refreshCharts', [
            'paymentsPerMonth' => $this->paymentsPerMonth,
            'paymentStatuses' => $this->paymentStatuses,
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard-statistics');
    }
}
