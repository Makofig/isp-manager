<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Events\ProviderUpdated;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::orderBy('nombre')->paginate(15);
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'contacto' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
            'mb_up' => 'required|integer|min:0',
            'mb_down' => 'required|integer|min:0',
            'precio_total' => 'required|numeric|min:0',
            'precio_por_mb' => 'nullable|numeric|min:0',
            'tipo' => 'required|in:internet,equipamiento,ambos',
            'notas' => 'nullable|string',
        ]);

        $provider = Proveedor::create($validated);
        ProviderUpdated::dispatch($provider, 'created');
        return redirect()->route('proveedores.index')->with('success', 'Provider created successfully.');
    }

    public function show(string $id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return view('proveedores.show', compact('proveedor'));
    }

    public function edit(string $id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, string $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'contacto' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
            'mb_up' => 'required|integer|min:0',
            'mb_down' => 'required|integer|min:0',
            'precio_total' => 'required|numeric|min:0',
            'precio_por_mb' => 'nullable|numeric|min:0',
            'tipo' => 'required|in:internet,equipamiento,ambos',
            'notas' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $proveedor->update($validated);
        ProviderUpdated::dispatch($proveedor, 'updated');
        return redirect()->route('proveedores.show', $id)->with('success', 'Provider updated.');
    }

    public function destroy(string $id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->delete();
        ProviderUpdated::dispatch($proveedor, 'deleted');
        return redirect()->route('proveedores.index')->with('success', 'Provider deleted.');
    }
}
