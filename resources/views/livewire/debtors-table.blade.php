<div>
    <!-- Search and Filters -->
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <x-search-input model="search" placeholder="Search debtors..." />
        <select wire:model.live="anio" class="input-dark w-full sm:w-24 text-sm">
            @foreach(range(2023, now()->year) as $y)
            <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
        </select>
        <select wire:model.live="mes" class="input-dark w-full sm:w-32 text-sm">
            @foreach(range(1,12) as $m)
            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
            @endforeach
        </select>
        <button wire:click="export" class="btn-danger text-sm whitespace-nowrap">
            Export PDF
        </button>
    </div>

    <!-- Table -->
    <div class="relative overflow-x-auto rounded-[8px] border border-dark-border">
        <table class="min-w-full">
            <thead class="bg-dark-elevated">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Client</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Contract</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Debt</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-4 py-3 text-right text-[11px] font-medium text-muted uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-border">
                @forelse($debtors as $client)
                <tr class="hover:bg-dark-elevated/50 transition-colors duration-150" data-table-row>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full overflow-hidden bg-dark-elevated flex-shrink-0">
                                <img
                                    class="w-8 h-8 object-cover"
                                    src="{{ $client->imagen
                                        ? asset('storage/clients/' . $client->imagen)
                                        : asset('images/default-avatar.png') }}"
                                    alt="{{ $client->nombre }}">
                            </div>
                            <div class="text-sm text-text-primary">{{ $client->nombre }} {{ $client->apellido }}</div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-text-secondary">{{ $client->contract->megabytes ?? 0 }} MB</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-medium text-danger">${{ number_format($client->contract->costo ?? 0, 2) }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <x-ui-badge variant="debtor">DEBTOR</x-ui-badge>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('payments.show', $client->pago_id) }}" class="p-2 rounded-[6px] text-muted hover:bg-dark-elevated hover:text-text-primary" title="View">
                                <x-icon name="eye" class="w-4 h-4" />
                            </a>
                            @if ($client->pago_estado == 1)
                            <span class="p-2 text-muted cursor-not-allowed" title="Already paid">
                                <x-icon name="check" class="w-4 h-4" />
                            </span>
                            @else
                            <a href="{{ route('payments.edit', $client->pago_id) }}" class="p-2 rounded-[6px] text-muted hover:bg-success/10 hover:text-success" title="Pay">
                                <x-icon name="check" class="w-4 h-4" />
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-12">
                        <x-ui-empty-state
                            title="No debtors found"
                            message="All clients are up to date with their payments."
                            icon="alert-triangle"
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <x-pagination :paginator="$debtors" />
    </div>
</div>
