<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payments;
use App\Models\Client;
use App\Models\Quota;
use App\Events\PaymentRegistered;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class paymentController extends Controller
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
        // Encontrar el pago
        $payment = Payments::findOrFail($id);

        // Validar los datos
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'coment' => 'required|string|max:255',
            'payment_date' => 'required|date',
            'voucher' => 'nullable|file|mimes:jpg,png,pdf|max:4096',
        ]);

        // Calcular nuevo abonado (acumular si ya tenía pagos parciales)
        $newAbonado = $payment->abonado + $validated['amount'];

        // Determinar estado: pagado si cubre el costo, parcial si no
        $estado = $newAbonado >= $payment->costo ? 1 : 0;

        // Armamos el array de actualización
        $data = [
            'abonado'    => $newAbonado,
            'pago_parcial' => $validated['amount'],
            'estado'     => $estado,
            'comentario' => $validated['coment'],
            'fecha_pago' => $validated['payment_date'],
        ];

        // Si subió un comprobante, lo guardamos
        if ($request->hasFile('voucher')) {
            $clientId = $payment->id_cliente;
            $path = $request->file('voucher')->store("vouchers/client_$clientId", 'public');
            $data['image'] = $path;
        }

        // Actualizar
        $payment->update($data);

        // Emitir evento WebSocket (solo si se marcó como pagado)
        if ($estado === 1) {
            PaymentRegistered::dispatch($payment);
        }

        $message = $estado === 1
            ? 'Payment completed successfully.'
            : "Partial payment registered. Remaining: $" . number_format($payment->remaining_amount, 2);

        return redirect()->route('clients.show', $payment->id_cliente)->with('success', $message);
    }

    /**
     * Retry failed payments for a specific client or quota.
     */
    public function retry(Request $request)
    {
        $request->validate([
            'client_id' => 'required_without:quota_id|exists:cliente,id',
            'quota_id' => 'required_without:client_id|exists:cuotas,id',
        ]);

        if ($request->filled('client_id')) {
            $client = Client::with('contract')->findOrFail($request->client_id);
            $quota = Quota::latest('created_at')->first();

            if (!$client->contract || !$quota) {
                return response()->json(['error' => 'Client has no contract or no quota exists'], 422);
            }

            $existing = Payments::where('id_cliente', $client->id)
                ->where('id_cuota', $quota->id)
                ->exists();

            if ($existing) {
                return response()->json(['message' => 'Payment already exists for this quota'], 200);
            }

            Payments::create([
                'id_cliente' => $client->id,
                'id_cuota' => $quota->id,
                'num_cuotas' => now()->month,
                'costo' => $client->contract->costo,
                'abonado' => 0,
                'estado' => 0,
            ]);

            return response()->json(['message' => 'Payment created for client'], 201);
        }

        $quota = Quota::findOrFail($request->quota_id);
        $clients = Client::with('contract')->get();
        $created = 0;

        foreach ($clients as $client) {
            if (!$client->contract) continue;

            $exists = Payments::where('id_cliente', $client->id)
                ->where('id_cuota', $quota->id)
                ->exists();

            if (!$exists) {
                Payments::create([
                    'id_cliente' => $client->id,
                    'id_cuota' => $quota->id,
                    'num_cuotas' => $quota->numero,
                    'costo' => $client->contract->costo,
                    'abonado' => 0,
                    'estado' => 0,
                ]);
                $created++;
            }
        }

        return response()->json(['message' => "Created {$created} missing payments for quota #{$quota->id}"], 201);
    }

    /**
     * Export payments to PDF.
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'quota_id' => 'nullable|exists:cuotas,id',
            'status' => 'nullable|in:0,1,all',
        ]);

        $query = Payments::with(['clients', 'cuota']);

        if ($request->filled('quota_id')) {
            $query->where('id_cuota', $request->quota_id);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('estado', $request->status);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('exports.payments', [
            'payments' => $payments,
            'quotaId' => $request->quota_id,
            'status' => $request->status ?? 'all',
        ])->setPaper('a4', 'landscape');

        return $pdf->download('payments_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
