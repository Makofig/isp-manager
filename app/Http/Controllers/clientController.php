<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contracts;
use App\Models\Access_Point;
use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Barryvdh\DomPDF\Facade\Pdf;

class clientController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Client::class);

        return view('clients.index');
    }

    public function debtors(){
        $debtors = Client::whereHas('pagos', function($query) {
            $query->where('estado', '0');
        })->paginate(10);
        return view('clients.debtors', compact('debtors'));
    }

    public function banned(){
        //$bannedClients = Client::where('is_banned', true)->paginate(10);
        return view('clients.banned'); //compact('bannedClients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Client::class);

        $contracts = Contracts::all();
        $points = Access_Point::all();
        return view('clients.create', compact('contracts', 'points'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Client::class);

        // guardar el cliente
        /*
        $client = new Client();
        $client->first_name = $request->input('first-name');
        $client->last_name = $request->input('last-name');
        $client->email = $request->input('email');
        $client->ip_address = $request->input('ip-address');
        */
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'ip_address' => 'nullable|ip|unique:cliente,ip',
            'phone' => 'required|string|max:20',
            'contracts_id' => 'required|exists:plan,id',
            'access_point_id' => 'required|exists:accespoint,id',
            'street_address' => 'required|string|max:255',
            'file_upload' => 'nullable|image|max:2048',
        ], [
            // Mensajes personalizados
            'first_name.required'      => 'The name cannot be empty.',
            'last_name.required'       => 'The surname cannot be empty.',
            'phone.required'           => 'A phone number is required.',
            'contracts_id.required'    => 'You must select a contract.',
            'contracts_id.exists'      => 'The selected contract is not valid.',
            'access_point_id.required' => 'You must select an access point.',
            'access_point_id.exists'   => 'The selected access point in not valid.',
            'street_address.required'  => 'The address is required.',
            'file_upload.image'        => 'The file must be a valid image.',
            'file_upload.max'          => 'The image cannot exceed 2 MB.',
        ]);
        // Guardar imagen si viene en la petición
        if ($request->hasFile('file_upload')) {
            $nombreImagen = time() . '.' . $request->file_upload->extension();
            $request->file_upload->storeAs('clients', $nombreImagen, 'public'); // carpeta storage/app/public/clients
            $validated['imagen'] = $nombreImagen; // Guardamos solo el nombre en DB
        }

        Client::create([
            'id_plan' => $validated['contracts_id'],
            'id_point' => $validated['access_point_id'],
            'nombre' => $validated['first_name'],
            'apellido' => $validated['last_name'],
            'direccion' => $validated['street_address'],
            'telefono' => $validated['phone'],
            'ip' => $validated['ip_address'],
            'imagen' => $validated['imagen'] ?? null
        ]);
        // $client->save();

        return redirect()->route('clients.create')->with('success', 'Client created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $client = Client::findOrFail($id);
        $this->authorize('view', $client);

        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $client = Client::findOrFail($id);
        $this->authorize('update', $client);

        $contracts = Contracts::all();
        $points = Access_Point::all();
        return view('clients.edit', compact('client', 'contracts', 'points'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $client = Client::findOrFail($id);
        $this->authorize('update', $client);

        // Recibir los datos para actualizar el cliente
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'ip_address' => 'nullable|ip|unique:cliente,ip,'. $id,
            'phone' => 'required|string|max:20',
            'contracts_id' => 'required|exists:plan,id',
            'access_point_id' => 'required|exists:accespoint,id',
            'street_address' => 'required|string|max:255',
            'file_upload' => 'nullable|image|max:2048',
        ], [
            // Mensajes personalizados
            'first_name.required'      => 'The name cannot be empty.',
            'last_name.required'       => 'The surname cannot be empty.',
            'phone.required'           => 'A phone number is required.',
            'contracts_id.required'    => 'You must select a contract.',
            'contracts_id.exists'      => 'The selected contract is not valid.',
            'access_point_id.required' => 'You must select an access point.',
            'access_point_id.exists'   => 'The selected access point in not valid.',
            'street_address.required'  => 'The address is required.',
            'file_upload.image'        => 'The file must be a valid image.',
            'file_upload.max'          => 'The image cannot exceed 2 MB.',
        ]);

        // Si viene nueva imagen
        if ($request->hasFile('file_upload')) {
            // Borrar la anterior si existe
            if ($client->imagen && Storage::disk('public')->exists('clients/' . $client->imagen)) {
                Storage::disk('public')->delete('clients/' . $client->imagen);
            }

            // Guardar nueva
            $nombreImagen = time() . '.' . $request->file_upload->extension();
            $request->file_upload->storeAs('clients', $nombreImagen, 'public');
            $validated['imagen'] = $nombreImagen;
        } else {
            // Mantener la actual si no se subió nueva
            $validated['imagen'] = $client->imagen;
        }

        $client->update([
            'id_plan' => $validated['contracts_id'],
            'id_point' => $validated['access_point_id'],
            'nombre' => $validated['first_name'],
            'apellido' => $validated['last_name'],
            'direccion' => $validated['street_address'],
            'telefono' => $validated['phone'],
            'ip' => $validated['ip_address'],
            'imagen' => $validated['imagen'] ?? null
        ]);
        // $client->save();

        return redirect()->route('clients.edit', $client->id)->with('success', 'Client successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);
        $this->authorize('delete', $client);

        // Eliminar un cliente seleccionado

        // Si tiene imagen, borrarla
        if ($client->imagen && Storage::disk('public')->exists("clients/{$client->imagen}")) {
            Storage::disk('public')->delete("clients/{$client->imagen}");
        }

        $client->delete();

        return redirect()->route('clients')->with('success', 'Client successfully removed.');
    }

    public function exportPdf($type)
    {
        // Si NO se envían año/mes → usar actuales
        $anio = request('anio') ?: now()->year; 
        $mes = request('mes') ?: now()->month; 
        // debug 
        // dd($anio, $mes); 

        // $anio = $anio ?: now()->year;
        // $mes  = $mes ?: now()->month;

        $query = Client::query()->with('contract'); 

        if ($type === 'banned') {
            $query->where('is_banned', 1); 
        }

        if ($type == 'debtors') {
            $query->whereHas('pagos', function ($q) use ($anio, $mes) {
                $q->where('estado', '0')
                  ->whereYear('created_at', $anio)
                  ->whereMonth('created_at', $mes);
            });
        }

        // === Filtro por año/mes para created_at ===
        // $query->whereYear('created_at', $anio)
        //     ->whereMonth('created_at', $mes);

        $clients = $query->get();

        $pdf = Pdf::loadView('exports.clients', compact('clients', 'type', 'anio', 'mes'))
            ->setPaper('a4', 'portrait'); 

        return $pdf->download("clients_{$type}_" . date('Ymd_His') . '.pdf');
    }
}
