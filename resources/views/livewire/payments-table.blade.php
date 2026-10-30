<div>
    <div class="relative overflow-x-auto rounded-[8px] border border-dark-border">
        <table class="min-w-full">
            @if($client)
            <thead class="bg-dark-elevated">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Amount</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Paid</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Payment Date</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-4 py-3 text-right text-[11px] font-medium text-muted uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-border">
                @foreach($payments as $payment)
                <tr class="hover:bg-dark-elevated/50 transition-colors duration-150">
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-text-primary">{{ $payment->cuota?->created_at?->format('M d, Y') ?? '—' }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-text-secondary">${{ number_format($payment->costo, 2) }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-text-secondary">${{ number_format($payment->abonado, 2) }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-text-secondary">{{ $payment->fecha_pago ?? '—' }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        @if ($payment->estado == 1)
                            <x-ui-badge variant="success">PAID</x-ui-badge>
                        @else
                            <x-ui-badge variant="danger">DUE</x-ui-badge>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('payments.show', $payment->id) }}" class="p-2 rounded-[6px] text-muted hover:bg-dark-elevated hover:text-text-primary" title="View">
                                <x-icon name="eye" class="w-4 h-4" />
                            </a>
                            @if ($payment->estado == 1)
                                <span class="p-2 text-muted cursor-not-allowed" title="Already paid">
                                    <x-icon name="check" class="w-4 h-4" />
                                </span>
                            @else
                                <a href="{{ route('payments.edit', $payment->id) }}" class="p-2 rounded-[6px] text-muted hover:bg-success/10 hover:text-success" title="Pay">
                                    <x-icon name="check" class="w-4 h-4" />
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            @else
            <tr>
                <td colspan="6" class="px-4 py-12">
                    <x-ui-empty-state title="No payments found" description="No payment information available for this client." />
                </td>
            </tr>
            @endif
        </table>
    </div>
    <!-- Pagination -->
    <div class="mt-4">
        <x-pagination :paginator="$payments" />
    </div>
</div>
