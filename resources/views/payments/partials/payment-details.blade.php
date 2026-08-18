<div class="stat-card" x-data="{ open: false }" @keydown.escape.window="open = false">
    <!-- Status Icon -->
    @if ($payment->estado == 1)
    <div class="flex flex-col items-center justify-center mb-6">
        <img src="{{ asset('images/icon-check.svg') }}" alt="Pago realizado" class="w-14 h-14 object-contain">
        <p class="text-success font-semibold text-lg mt-3">Paid: $ {{ number_format($payment->costo, 2) }}</p>
    </div>
    @else
    <div class="flex flex-col items-center justify-center mb-6">
        <img src="{{ asset('images/icon-cancel.svg') }}" alt="Pago pendiente" class="w-14 h-14 object-contain">
        <p class="text-danger font-semibold text-lg mt-3">Pending: $ {{ number_format($payment->costo, 2) }}</p>
    </div>
    @endif

    <!-- Details -->
    <h4 class="text-sm font-semibold text-text-primary mb-4">Payment Details</h4>

    <div class="space-y-3">
        <div class="flex items-center justify-between py-2 border-b border-dark-border">
            <span class="text-xs text-muted uppercase tracking-wider">Amount</span>
            <span class="text-sm text-text-secondary">${{ number_format($payment->abonado > 0 ? $payment->abonado : 0, 2) }}</span>
        </div>
        <div class="flex items-center justify-between py-2 border-b border-dark-border">
            <span class="text-xs text-muted uppercase tracking-wider">Comment</span>
            <span class="text-sm text-text-secondary">{{ $payment->comentario ?? '—' }}</span>
        </div>
        <div class="flex items-center justify-between py-2 border-b border-dark-border">
            <span class="text-xs text-muted uppercase tracking-wider">Created</span>
            <span class="text-sm text-text-secondary">{{ $payment->created_at->format('M d, Y') }}</span>
        </div>
        <div class="flex items-center justify-between py-2 border-b border-dark-border">
            <span class="text-xs text-muted uppercase tracking-wider">Payment Date</span>
            <span class="text-sm text-text-secondary">{{ $payment->fecha_pago ?? 'Not paid yet' }}</span>
        </div>
    </div>

    <!-- Voucher -->
    <div class="mt-6 pt-4 border-t border-dark-border">
        <h4 class="text-sm font-semibold text-text-primary mb-3">Payment Voucher</h4>
        @if ($payment->image)
        <div class="flex items-center gap-4">
            <img src="{{ asset('storage/' . $payment->image) }}"
                alt="Payment Voucher"
                class="w-20 h-20 object-cover rounded-[8px] cursor-pointer hover:opacity-80 transition"
                @click="open = true">
            <div class="flex-1">
                <a href="{{ asset('storage/' . $payment->image) }}" class="text-sm text-accent hover:text-accent-hover">Download Voucher</a>
                <p class="text-xs text-muted mt-1">Click image to preview</p>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="fixed inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm z-50 p-4"
            @click.self="open = false"
            style="display: none;">
            <div class="relative bg-dark-surface border border-dark-border p-4 rounded-[12px] w-full max-w-4xl max-h-[90vh] flex flex-col">
                <button @click="open = false" class="absolute top-3 right-3 text-muted hover:text-text-primary text-2xl">&times;</button>
                <div class="flex justify-center items-center flex-1 overflow-auto">
                    <img src="{{ asset('storage/' . $payment->image) }}" alt="Voucher Fullscreen" class="w-full h-auto max-h-[75vh] object-contain rounded-[8px]">
                </div>
                <div class="mt-4 flex justify-center">
                    <a href="{{ asset('storage/' . $payment->image) }}" download class="btn-primary text-sm">Descargar Comprobante</a>
                </div>
            </div>
        </div>
        @else
        <p class="text-sm text-muted">No voucher available</p>
        @endif
    </div>
</div>
