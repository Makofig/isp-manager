<?php

namespace App\Http\Controllers;

use App\Models\Access_Point;
use Illuminate\Http\Request;

class AccessPointController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mostrar todos los puntos de acceso
        $points = Access_Point::paginate(10); 
        return view('access-point.index', compact('points'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('access-point.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Recuperar los datos para guardarlos 
        $validated = $request->validate([
            'ssid' => 'required|string|max:255',
            'frequency' => 'required|string|max:100',
            'ip_address' => 'nullable|ip',
            'location' => 'required|string|max:255',
        ],[
            'ssid.required' => 'The SSID is required.',
            'frequency.required' => 'Frequency is mandatory.',
            'ip_address.ip' => 'The IP address must be valid.',
            'location.required' => 'Location is required.',
        ]);

        Access_Point::create([
            'ssid' => $validated['ssid'],
            'frecuencia' => $validated['frequency'],
            'ip_ap' => $validated['ip_address'],
            'localidad' => $validated['location'],
        ]);

        return redirect()->route('access-point.create')->with('success', 'Access Point created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // mostrar la información de un Access Point 
        $access_point = Access_Point::findOrFail($id);
        $clients = $access_point->clientes()->paginate(10); 

        return view('access-point.show', compact('access_point', 'clients'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Editar un punto de acceso
        $point = Access_Point::findOrFail($id);
        return view('access-point.edit', compact('point'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Recibimos los datos del contrato para guardarlos 
        $validated= $request->validate([
            'ssid' => 'required|string|max:255',
            'frequency' => 'required|string|max:100',
            'ip_address' => 'nullable|ip',
            'location' => 'required|string|max:255',
        ],[
            'ssid.required' => 'The SSID is required.',
            'frequency.required' => 'Frequency is mandatory.',
            'ip_address.ip' => 'The IP address must be valid.',
            'location.required' => 'Location is required.',
        ]);

        Access_Point::where('id', $id)->update([
            'ssid' => $validated['ssid'],
            'frecuencia' => $validated['frequency'],
            'ip_ap' => $validated['ip_address'],
            'localidad' => $validated['location'],
        ]);

        return redirect()->route('access-point.edit', $id)->with('success', 'The Access Point was successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Eliminar un punto de acceso seleccionado
        $point = Access_Point::findOrFail($id);
        $point->delete();

        return redirect()->route('access-point')->with('success', 'Acceess Point successfully removed.');
    }
}
