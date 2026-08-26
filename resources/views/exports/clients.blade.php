<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }} — Export</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 20px; }
        h2 { margin: 0 0 4px; font-size: 16px; }
        .subtitle { color: #666; font-size: 11px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f4f4f4; text-align: left; padding: 6px 8px; border: 1px solid #ddd; font-size: 10px; text-transform: uppercase; letter-spacing: .3px; }
        td { padding: 5px 8px; border: 1px solid #ddd; }
        tr:nth-child(even) td { background: #fafafa; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 9px; font-weight: 600; }
        .badge-ok   { background: #d1fae5; color: #065f46; }
        .badge-ban  { background: #fee2e2; color: #991b1b; }
        .empty      { color: #aaa; font-style: italic; }
    </style>
</head>
<body>

<h2>{{ $title }}</h2>
<p class="subtitle">
    Generado: {{ now()->format('d/m/Y H:i') }}
    &middot; {{ $clients->count() }} cliente{{ $clients->count() !== 1 ? 's' : '' }}
</p>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Teléfono</th>
            <th>IP</th>
            <th>Contrato (MB)</th>
            <th>Dirección</th>
            <th>Access Point</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @forelse($clients as $i => $client)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $client->nombre }}</td>
                <td>{{ $client->apellido }}</td>
                <td>{{ $client->telefono ?: '—' }}</td>
                <td>{{ $client->ip ?: '—' }}</td>
                <td>{{ $client->contract->megabytes ?? 'N/A' }} MB</td>
                <td>{{ $client->direccion ?: '—' }}</td>
                <td>{{ $client->accessPoint->ssid ?? 'N/A' }}</td>
                <td>
                    @if($client->is_banned)
                        <span class="badge badge-ban">Baneado</span>
                    @else
                        <span class="badge badge-ok">Activo</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="empty">No se encontraron clientes.</td>
            </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
