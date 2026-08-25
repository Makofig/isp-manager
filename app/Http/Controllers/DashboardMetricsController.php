<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use App\Models\Proveedor;
use App\Models\Gasto;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardMetricsController extends Controller
{
    public function profitabilityMetrics(Request $request)
    {
        $anio = $request->get('anio', now()->year);
        $mes = $request->get('mes', now()->month);

        // Ingresos del mes
        $ingresos = Payments::whereYear('fecha_pago', $anio)
            ->whereMonth('fecha_pago', $mes)
            ->sum('abonado');

        // Costos de proveedores (mensual)
        $costoProveedores = Proveedor::activos()->sum('precio_total');

        // Gastos del mes
        $gastos = Gasto::whereYear('fecha_gasto', $anio)
            ->whereMonth('fecha_gasto', $mes)
            ->sum('monto');

        // Totales
        $costoTotal = $costoProveedores + $gastos;
        $gananciaNeta = $ingresos - $costoTotal;
        $margen = $ingresos > 0 ? ($gananciaNeta / $ingresos) * 100 : 0;

        return response()->json([
            'periodo' => [
                'anio' => (int) $anio,
                'mes' => (int) $mes,
                'nombre_mes' => Carbon::create($anio, $mes)->format('F'),
            ],
            'ingresos' => round($ingresos, 2),
            'costo_proveedores' => round($costoProveedores, 2),
            'gastos_operativos' => round($gastos, 2),
            'costo_total' => round($costoTotal, 2),
            'ganancia_neta' => round($gananciaNeta, 2),
            'margen_porcentaje' => round($margen, 2),
            'es_rentable' => $gananciaNeta > 0,
        ]);
    }
}
