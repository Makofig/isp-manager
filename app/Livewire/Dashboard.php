<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Payments;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

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

    public function cacheData()
    {
        return cache()->remember('stats_global', 30, function () {
            return [
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
                    ->mapWithKeys(fn ($row) => [
                        $row->month => [
                            'totalCuotas' => $row->totalCuotas,
                            'totalPagado' => $row->totalPagado,
                            'deuda' => $row->totalCuotas - $row->totalPagado,
                        ],
                    ])
                    ->toArray(),
                'paymentStatuses' => Payments::select('estado', DB::raw('count(*) as total'))
                    ->groupBy('estado')
                    ->pluck('total', 'estado')
                    ->toArray(),
                'topDebtors' => Client::withSum(['pagos as deuda' => function ($q) {
                    $q->where('estado', '0'); // cuotas no pagadas
                }], 'costo')
                    ->orderByDesc('deuda')
                    ->take(5)
                    ->get()
                    ->toArray(),
                'topPayers' => Client::withSum(['pagos as total_paid' => function ($q) {
                    $q->where('estado', 1); // Solo pagos confirmados
                }], 'abonado')
                    ->orderByDesc('total_paid')
                    ->take(5)
                    ->get()
                    ->toArray(),
            ];
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
