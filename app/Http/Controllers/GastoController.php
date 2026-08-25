<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Events\ExpenseRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GastoController extends Controller
{
    public function index(Request $request)
    {
        $query = Gasto::with('usuario');

        if ($request->filled('categoria')) {
            $query->categoria($request->categoria);
        }

        if ($request->filled('desde') && $request->filled('hasta')) {
            $query->entreFechas($request->desde, $request->hasta);
        }

        $gastos = $query->orderBy('fecha_gasto', 'desc')->paginate(20);

        $categorias = [
            'cables_utp' => 'Cables UTP',
            'herramientas' => 'Herramientas',
            'rj45' => 'RJ45',
            'routers_clientes' => 'Routers para Clientes',
            'equipos_nodos' => 'Equipos para Nodos',
            'fibra_optica' => 'Fibra Óptica',
            'antenas' => 'Antenas',
            'postes_torres' => 'Postes/Torres',
            'combustible' => 'Combustible',
            'salarios' => 'Salarios',
            'alquiler' => 'Alquiler',
            'servicios' => 'Servicios',
            'reparaciones' => 'Reparaciones',
            'otros' => 'Otros',
        ];

        return view('gastos.index', compact('gastos', 'categorias'));
    }

    public function create()
    {
        $categorias = [
            'cables_utp' => 'Cables UTP',
            'herramientas' => 'Herramientas',
            'rj45' => 'RJ45',
            'routers_clientes' => 'Routers para Clientes',
            'equipos_nodos' => 'Equipos para Nodos',
            'fibra_optica' => 'Fibra Óptica',
            'antenas' => 'Antenas',
            'postes_torres' => 'Postes/Torres',
            'combustible' => 'Combustible',
            'salarios' => 'Salarios',
            'alquiler' => 'Alquiler',
            'servicios' => 'Servicios',
            'reparaciones' => 'Reparaciones',
            'otros' => 'Otros',
        ];

        return view('gastos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'concepto' => 'required|string|max:200',
            'categoria' => 'required|in:cables_utp,herramientas,rj45,routers_clientes,equipos_nodos,fibra_optica,antenas,postes_torres,combustible,salarios,alquiler,servicios,reparaciones,otros',
            'monto' => 'required|numeric|min:0',
            'fecha_gasto' => 'required|date',
            'proveedor' => 'nullable|string|max:150',
            'comprobante' => 'nullable|file|mimes:jpg,png,pdf|max:4096',
            'notas' => 'nullable|string',
        ]);

        if ($request->hasFile('comprobante')) {
            $path = $request->file('comprobante')->store('comprobantes', 'public');
            $validated['comprobante'] = $path;
        }

        $validated['user_id'] = auth()->id();
        $gasto = Gasto::create($validated);
        ExpenseRegistered::dispatch($gasto);

        return redirect()->route('gastos.index')->with('success', 'Expense registered.');
    }

    public function edit(string $id)
    {
        $gasto = Gasto::findOrFail($id);

        $categorias = [
            'cables_utp' => 'Cables UTP',
            'herramientas' => 'Herramientas',
            'rj45' => 'RJ45',
            'routers_clientes' => 'Routers para Clientes',
            'equipos_nodos' => 'Equipos para Nodos',
            'fibra_optica' => 'Fibra Óptica',
            'antenas' => 'Antenas',
            'postes_torres' => 'Postes/Torres',
            'combustible' => 'Combustible',
            'salarios' => 'Salarios',
            'alquiler' => 'Alquiler',
            'servicios' => 'Servicios',
            'reparaciones' => 'Reparaciones',
            'otros' => 'Otros',
        ];

        return view('gastos.edit', compact('gasto', 'categorias'));
    }

    public function update(Request $request, string $id)
    {
        $gasto = Gasto::findOrFail($id);

        $validated = $request->validate([
            'concepto' => 'required|string|max:200',
            'categoria' => 'required|in:cables_utp,herramientas,rj45,routers_clientes,equipos_nodos,fibra_optica,antenas,postes_torres,combustible,salarios,alquiler,servicios,reparaciones,otros',
            'monto' => 'required|numeric|min:0',
            'fecha_gasto' => 'required|date',
            'proveedor' => 'nullable|string|max:150',
            'comprobante' => 'nullable|file|mimes:jpg,png,pdf|max:4096',
            'notas' => 'nullable|string',
        ]);

        if ($request->hasFile('comprobante')) {
            if ($gasto->comprobante) {
                Storage::disk('public')->delete($gasto->comprobante);
            }
            $path = $request->file('comprobante')->store('comprobantes', 'public');
            $validated['comprobante'] = $path;
        }

        $gasto->update($validated);
        ExpenseRegistered::dispatch($gasto);
        return redirect()->route('gastos.index')->with('success', 'Expense updated.');
    }

    public function destroy(string $id)
    {
        $gasto = Gasto::findOrFail($id);
        if ($gasto->comprobante) {
            Storage::disk('public')->delete($gasto->comprobante);
        }
        $gasto->delete();
        ExpenseRegistered::dispatch($gasto);
        return redirect()->route('gastos.index')->with('success', 'Expense deleted.');
    }
}
