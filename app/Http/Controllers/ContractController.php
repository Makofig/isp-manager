<?php

namespace App\Http\Controllers;

use App\Models\Contracts; 
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // obtener todos los contratos 
        $contracts = Contracts::paginate(10); 

        return view('contracts.index', compact('contracts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Retornar la vista para crear el contrato
        return view('contracts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Recibimos los datos del contrato para guardarlos 
        $validated= $request->validate([
            'name' => 'required|string|max:255',
            'megabytes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ],[
            'name.required' => 'The name is required.',
            'megabytes.required' => 'Megabytes is mandatory.',
            'price.required' => 'The price is mandatory.',
        ]);

        Contracts::create([
            'nombre' => $validated['name'],
            'megabytes' => $validated['megabytes'],
            'costo' => $validated['price'],
        ]);

        return redirect()->route('contracts.create')->with('success', 'The contracts was created correctly.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Obtener el contrato por su ID
        $contract = Contracts::findOrFail($id);
        return view('contracts.edit', compact('contract'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Recibimos los datos del contrato para guardarlos 
        $validated= $request->validate([
            'name' => 'required|string|max:255',
            'megabytes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ],[
            'name.required' => 'The name is required.',
            'megabytes.required' => 'Megabytes is mandatory.',
            'price.required' => 'The price is mandatory.',
        ]);

        Contracts::where('id', $id)->update([
            'nombre' => $validated['name'],
            'megabytes' => $validated['megabytes'],
            'costo' => $validated['price'],
        ]);

        return redirect()->route('contracts.edit', $id)->with('success', 'The contracts was updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Eliminar un contrato seleccionado
        $contract = Contracts::findOrFail($id);


        $contract->delete();

        return redirect()->route('contracts')->with('success', 'Contract successfully deleted.');
    }
}
