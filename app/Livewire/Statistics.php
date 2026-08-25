<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Client;
use App\Models\Payments;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $cacheKey = "stats_{$this->anio}_{$this->mes}";
        $data = cache()->remember($cacheKey, 300, fn() => $this->computeStats());

        $this->dispatch('updateCharts', [
            'recaudado' => $data['recaudado'],
            'pendiente' => $data['pendiente'],
            'rangos'    => $data['rangos'],
        ]);

        return view('livewire.statistics', array_merge($data, [
            'mes' => $this->mes,
            'anio' => $this->anio,
        ]));
    }

    private function computeStats(): array
    {
        $totalClientes = Client::count();
        $bannedClients = Client::where('is_banned', true)->count();

        $paymentsQuery = Payments::whereMonth('created_at', $this->mes)
            ->whereYear('created_at', $this->anio);

        $recaudado = (clone $paymentsQuery)
            ->whereNotNull('fecha_pago')
            ->sum('abonado');

        $totalEsperado = (clone $paymentsQuery)->sum('costo');
        $pendiente = $totalEsperado - $recaudado;

        $clientesConPago = Payments::whereNotNull('fecha_pago')
            ->whereMonth('fecha_pago', $this->mes)
            ->whereYear('fecha_pago', $this->anio)
            ->where('estado', 1)
            ->where('abonado', '>', 0)
            ->distinct('id_cliente')
            ->count('id_cliente');

        $morosidad = $totalClientes > 0
            ? round((($totalClientes - $clientesConPago) / $totalClientes) * 100, 2)
            : 0;

        $diasMes = Carbon::create($this->anio, $this->mes)->daysInMonth;
        $rangos = [
            '1-10'  => $this->pagosEnRango(1, 10),
            '11-20' => $this->pagosEnRango(11, 20),
            '>21'   => $this->pagosEnRango(21, $diasMes),
        ];

        $deudores = $totalClientes - $clientesConPago;

        $morosos = Client::whereNotIn('id', function ($q) {
            $q->select('id_cliente')
                ->from('pagos')
                ->whereNotNull('fecha_pago')
                ->whereMonth('fecha_pago', $this->mes)
                ->whereYear('fecha_pago', $this->anio)
                ->where('estado', 1);
        })->with('contract')->get();

        return compact(
            'totalClientes', 'bannedClients', 'recaudado', 'pendiente',
            'clientesConPago', 'morosidad', 'rangos', 'deudores', 'morosos'
        );
    }

    private function pagosEnRango(int $desde, int $hasta): int
    {
        return Payments::whereNotNull('fecha_pago')
            ->whereMonth('fecha_pago', $this->mes)
            ->whereYear('fecha_pago', $this->anio)
            ->whereBetween(DB::raw('DAY(fecha_pago)'), [$desde, $hasta])
            ->count();
    }
}
