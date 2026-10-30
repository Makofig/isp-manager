<x-app-layout>
    <x-page-header
        title="Payment Details"
        description="Payment from {{ $payment->clients?->nombre ?? 'N/A' }} {{ $payment->clients?->apellido ?? '' }}"
    />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Client Info -->
        <div class="lg:col-span-1">
            <div class="stat-card">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-[10px] overflow-hidden bg-dark-elevated mb-3">
                        <img
                            class="w-full h-full object-cover"
                            src="{{ $payment->clients?->imagen
                                ? asset('storage/clients/' . $payment->clients->imagen)
                                : asset('images/default-avatar.png') }}"
                            alt="{{ $payment->clients?->nombre }}">
                    </div>
                    <h3 class="text-sm font-semibold text-text-primary">{{ $payment->clients?->nombre }} {{ $payment->clients?->apellido }}</h3>
                </div>

                <div class="mt-4 pt-4 border-t border-dark-border space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted uppercase tracking-wider">Email</span>
                        <span class="text-sm text-text-secondary">{{ $payment->clients?->email ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted uppercase tracking-wider">Phone</span>
                        <span class="text-sm text-text-secondary">{{ $payment->clients?->telefono ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted uppercase tracking-wider">IP</span>
                        <span class="text-sm text-text-secondary font-mono">{{ $payment->clients?->ip ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted uppercase tracking-wider">Address</span>
                        <span class="text-sm text-text-secondary text-right max-w-[60%]">{{ $payment->clients?->direccion ?? '—' }}</span>
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-2">
                    <a href="{{ route('clients.show', $payment->clients?->id) }}" class="btn-secondary text-xs flex-1">View Client</a>
                    <a href="{{ url()->previous() }}" class="btn-ghost text-xs flex-1">Back</a>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="lg:col-span-2">
            @include('payments.partials.payment-details', ['payment' => $payment])
        </div>
    </div>
</x-app-layout>
