<x-app-layout>
    <x-page-header
        title="Client Details"
        description="{{ $client->nombre }} {{ $client->apellido }}"
    />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Client Info Card -->
        <div class="lg:col-span-1">
            <div class="stat-card">
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-[12px] overflow-hidden bg-dark-elevated mb-4">
                        <img
                            class="w-full h-full object-cover"
                            src="{{ $client->imagen
                                ? asset('storage/clients/' . $client->imagen)
                                : asset('images/default-avatar.png') }}"
                            alt="{{ $client->nombre }}">
                    </div>
                    <h3 class="text-base font-semibold text-text-primary">{{ $client->nombre }} {{ $client->apellido }}</h3>
                    <div class="mt-2">
                        @if ($client->is_banned)
                            <x-ui-badge variant="banned">BANNED</x-ui-badge>
                        @else
                            <x-ui-badge variant="active">ACTIVE</x-ui-badge>
                        @endif
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-dark-border space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted uppercase tracking-wider">Email</span>
                        <span class="text-sm text-text-secondary">{{ $client->email ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted uppercase tracking-wider">Phone</span>
                        <span class="text-sm text-text-secondary">{{ $client->telefono ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted uppercase tracking-wider">IP</span>
                        <span class="text-sm text-text-secondary font-mono">{{ $client->ip ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted uppercase tracking-wider">Address</span>
                        <span class="text-sm text-text-secondary text-right max-w-[60%]">{{ $client->direccion ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted uppercase tracking-wider">Contract</span>
                        <span class="text-sm text-text-secondary">{{ $client->contract->megabytes ?? '—' }} MB</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted uppercase tracking-wider">Access Point</span>
                        <span class="text-sm text-text-secondary">{{ $client->accessPoint->ssid ?? '—' }}</span>
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-2">
                    <a href="{{ route('clients.edit', $client->id) }}" class="btn-secondary text-xs flex-1">Edit</a>
                    <a href="{{ route('clients') }}" class="btn-ghost text-xs flex-1">Back</a>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="lg:col-span-2">
            <x-page-header title="Payment History" description="All payments for this client" />
            @livewire('payments-table', ['clientId' => $client->id])
        </div>
    </div>
</x-app-layout>
