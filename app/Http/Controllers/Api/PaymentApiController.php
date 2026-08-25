<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payments;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Payments::with(['clients', 'cuota']);

        if ($request->filled('client_id')) {
            $query->where('id_cliente', $request->client_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('quota_id')) {
            $query->where('id_cuota', $request->quota_id);
        }

        $payments = $query->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 20));

        return response()->json($payments);
    }

    public function show(string $id)
    {
        $payment = Payments::with(['clients', 'quota'])->findOrFail($id);
        return response()->json($payment);
    }

    public function update(Request $request, string $id)
    {
        $payment = Payments::findOrFail($id);

        $validated = $request->validate([
            'abonado' => 'required|numeric|min:0',
            'estado' => 'required|in:0,1',
            'comentario' => 'nullable|string|max:255',
            'fecha_pago' => 'nullable|date',
        ]);

        $payment->update($validated);

        return response()->json([
            'message' => 'Payment updated successfully',
            'payment' => $payment->fresh(),
        ]);
    }
}
