<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Payments;
use Carbon\Carbon;
use Livewire\Component;

class Statistics extends Component
{
    public $mes;
    public $anio;

    public function mount()
    {
        $this->mes = Carbon::now()->month;
        $this->anio = Carbon::now()->year;
    }

    public function render()
    {
        $cacheKey = "stats_{$this->mes}_{$this->anio}";

        // Cache stats for 30 min to avoid repeated expensive queries
        $stats = cache()->remember($cacheKey, 30, function () {
            // Total de clientes (no filtered by month, always returned)
            $totalClientes = Client::count();

            // Clientes baneados
            $bannedClients = Client::where('is_banned', true)->count();

            // Single raw query to get all payments stats for the period
            $payments = Payments::whereNotNull('fecha_pago')
                ->whereMonth('fecha_pago', $this->mes)
                ->whereYear('fecha_pago', $this->anio)
                ->selectRaw('
                    SUM(abonado) as recaudado,
                    COUNT(DISTINCT CASE WHEN estado = 1 AND abonado > 0 THEN id_cliente END) as clientes_con_pago,
                    SUM(CASE WHEN DAY(fecha_pago) BETWEEN 1 AND 10 THEN 1 ELSE 0 END) as rango_1_10,
                    SUM(CASE WHEN DAY(fecha_pago) BETWEEN 11 AND 20 THEN 1 ELSE 0 END) as rango_11_20,
                    SUM(CASE WHEN DAY(fecha_pago) > 21 THEN 1 ELSE 0 END) as rango_21
                ')
                ->first();

            // Total esperado from created_at (quota generation date)
            $totalEsperado = Payments::whereNotNull('created_at')
                ->whereMonth('created_at', $this->mes)
                ->whereYear('created_at', $this->anio)
                ->sum('costo');

            // Clients who did NOT pay by day 15
            $morosos = Client::whereNotIn('id', function ($q) {
                $q->select('id_cliente')
                    ->from('pagos')
                    ->whereNotNull('fecha_pago')
                    ->whereMonth('fecha_pago', $this->mes)
                    ->whereYear('fecha_pago', $this->anio)
                    ->whereDay('fecha_pago', '<=', 15);
            })->get();

            return [
                'totalClientes' => $totalClientes,
                'bannedClients' => $bannedClients,
                'recaudado' => $payments->recaudado ?? 0,
                'clientesConPago' => $payments->clientes_con_pago ?? 0,
                'totalEsperado' => $totalEsperado,
                'rango_1_10' => $payments->rango_1_10 ?? 0,
                'rango_11_20' => $payments->rango_11_20 ?? 0,
                'rango_21' => $payments->rango_21 ?? 0,
                'morosos' => $morosos,
            ];
        });

        // Derived values
        $pendiente = $stats['totalEsperado'] - $stats['recaudado'];
        $morosidad = $stats['totalClientes'] > 0
            ? round((($stats['totalClientes'] - $stats['clientesConPago']) / $stats['totalClientes']) * 100, 2)
            : 0;
        $deudores = $stats['totalClientes'] - $stats['clientesConPago'];

        $rangos = [
            '1-10' => $stats['rango_1_10'],
            '11-20' => $stats['rango_11_20'],
            '>21' => $stats['rango_21'],
        ];

        $this->dispatch('updateCharts', [
            'recaudado' => $stats['recaudado'],
            'pendiente' => $pendiente,
            'rangos' => $rangos,
        ]);

        return view('livewire.statistics', [
            'totalClientes' => $stats['totalClientes'],
            'clientesConPago' => $stats['clientesConPago'],
            'clientesBaneados' => $stats['bannedClients'],
            'recaudado' => $stats['recaudado'],
            'pendiente' => $pendiente,
            'morosidad' => $morosidad,
            'rangos' => $rangos,
            'deudores' => $deudores,
            'morosos' => $stats['morosos'],
            'mes' => $this->mes,
            'anio' => $this->anio,
        ]);
    }
}
