<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Quota;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Payments;
use App\Services\MercadoPagoService;

class PaymentController extends Controller
{
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
        // mostrar detalles del pago
        $payment = Payments::findOrFail($id);

        //return view('payments.show', ['payment' => $payment]);
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // editar los detalles del pago
        $payment = Payments::findOrFail($id);

        //return view('payments.edit', ['payment' => $payment]);
        return view('payments.edit', compact('payment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validar los datos 
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'coment' => 'required|string|max:255',
            'payment_date' => 'required|date',
            'voucher' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        // Encontrar el pago
        $payment = Payments::findOrFail($id);

        // Armamos el array de actualización
        $data = [
            'abonado' => $validated['amount'],
            'estado' => '1',
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

        // Actualizar
        $payment->update($data);

        return redirect()->route('clients.show', $payment->id_cliente)->with('success', 'Payment updated successfully.');
    }

    public function generarMensuales(Request $request)
    {
        // 🔐 Seguridad simple por API Key
        if ($request->bearerToken() !== config('app.api_key')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $mes = Carbon::now()->format('Y-m');

        // 👉 instanciación LAZY (clave)
        $mpService = app(MercadoPagoService::class);

        $clientes = Client::where('is_banned', false)
            ->with('contract')
            ->get();

        $response = [];

        foreach ($clientes as $cliente) {

            // 🛑 Idempotencia
            $yaExiste = Payments::where('id_cliente', $cliente->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->exists();

            if ($yaExiste) {
                continue;
            }

            // 1️⃣ Cuota
            $cuota = Quota::create([
                'numero' => $mes
            ]);

            // 2️⃣ Pago
            $pago = Payments::create([
                'id_cliente' => $cliente->id,
                'id_cuota' => $cuota->id,
                'num_cuotas' => 1,
                'costo' => $cliente->contract->costo,
                'estado' => false
            ]);

            // 3️⃣ Link MercadoPago
            $linkPago = $mpService->crearLinkPago([
                'title' => "Internet {$mes} - {$cliente->contract->nombre}",
                'price' => $cliente->contract->costo,
                'external_reference' => (string) $pago->id,
                'description' => "Monthly payment for {$cliente->nombre} {$cliente->apellido}",
            ]);

            // 4️⃣ Guardar link
            $pago->update([
                'link_pago' => $linkPago
            ]);

            $response[] = [
                'cliente_id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'email' => $cliente->email,
                'telefono' => $cliente->telefono,
                'link_pago' => $linkPago
            ];
        }

        return response()->json([
            'mes' => $mes,
            'cantidad' => count($response),
            'pagos' => $response
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
