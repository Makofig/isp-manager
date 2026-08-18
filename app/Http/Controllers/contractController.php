<?php

namespace App\Http\Controllers;

use App\Models\Contracts;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Client;

class contractController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Contracts::class);

        $contracts = Contracts::paginate(10); 

        return view('contracts.index', compact('contracts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Contracts::class);

        return view('contracts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Contracts::class);

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
        $contract = Contracts::findOrFail($id);
        $this->authorize('update', $contract);

        return view('contracts.edit', compact('contract'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $contract = Contracts::findOrFail($id);
        $this->authorize('update', $contract);

        // Recibimos los datos para actualizar el contrato
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
        $contract = Contracts::findOrFail($id);
        $this->authorize('delete', $contract);

        // Eliminar un contrato seleccionado

        // Contar los clientes asociados a este contrato 
        $clientsCount = Client::where('id_plan', $contract->id)->count();

        if ($clientsCount > 0) {
            return redirect()
                ->route('contracts')
                ->with('error', "Cannot delete contract {$contract->nombre} because it has associated {$clientsCount} clients.");
        }


        $contract->delete();

        return redirect()->route('contracts')->with('success', 'Contract successfully deleted.');
    }
}
