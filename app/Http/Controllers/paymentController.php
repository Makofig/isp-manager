<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Payments;

class paymentController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payments::findOrFail($id);
        $this->authorize('view', $payment);

        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $payment = Payments::findOrFail($id);
        $this->authorize('update', $payment);

        return view('payments.edit', compact('payment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $payment = Payments::findOrFail($id);
        $this->authorize('update', $payment);

        // Validar los datos 
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'coment' => 'required|string|max:255',
            'payment_date' => 'required|date',
            'voucher' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]); 

        // Armamos el array de actualización
        $data = [
            'abonado'    => $validated['amount'],
            'comentario' => $validated['coment'],
            'fecha_pago' => $validated['payment_date'],
        ];

        // Si subió un comprobante, lo guardamos
        if ($request->hasFile('voucher')) {
            // Usamos el ID del cliente para la carpeta
            $clientId = $payment->id_cliente; // suponiendo que Payments tiene relación con Client
            $path = $request->file('voucher')->store("vouchers/client_$clientId", 'public');
            $data['image'] = $path;
        }

        // Actualizar (estado is not in $fillable to prevent mass assignment,
        // so we set it explicitly below instead of through the validated array)
        $payment->update($data);

        // Explicitly set estado (not through mass assignment, use forceFill)
        $payment->forceFill(['estado' => 1])->save();

        return redirect()->route('clients.show', $payment->id_cliente)->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
