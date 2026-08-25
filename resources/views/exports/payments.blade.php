<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payments Export</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background: #f4f4f4; }
        .paid { color: green; font-weight: bold; }
        .pending { color: red; font-weight: bold; }
    </style>
</head>
<body>

<h2>
    Payments Report -
    @if($quotaId) Quota #{{ $quotaId }} @else All Quotas @endif
    @if($status !== 'all') | Status: {{ $status == '1' ? 'Paid' : 'Pending' }} @endif
</h2>

<p>Generated: {{ now()->format('Y-m-d H:i') }}</p>

<table>
    <thead>
        <tr>
            <th>Client</th>
            <th>Quota</th>
            <th>Month</th>
            <th>Amount</th>
            <th>Paid</th>
            <th>Payment Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($payments as $payment)
        <tr>
            <td>{{ $payment->clients->nombre ?? 'N/A' }} {{ $payment->clients->apellido ?? '' }}</td>
            <td>#{{ $payment->id_cuota }}</td>
            <td>{{ $payment->num_cuotas }}</td>
            <td>${{ number_format($payment->costo, 2) }}</td>
            <td>${{ number_format($payment->abonado, 2) }}</td>
            <td>{{ $payment->fecha_pago ?? '-' }}</td>
            <td class="{{ $payment->estado ? 'paid' : 'pending' }}">
                {{ $payment->estado ? 'Paid' : 'Pending' }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align: center;">No payments found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<tfoot>
    <tr>
        <td colspan="3"><strong>Total</strong></td>
        <td><strong>${{ number_format($payments->sum('costo'), 2) }}</strong></td>
        <td><strong>${{ number_format($payments->sum('abonado'), 2) }}</strong></td>
        <td colspan="2"></td>
    </tr>
</tfoot>

</body>
</html>
