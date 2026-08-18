<div>
    @if (session('success'))
    <script>
        window.toast.success(`{{ session('success') }}`);
    </script>
    @endif

    <!-- Search and Filter -->
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <x-search-input model="search" placeholder="Search clients..." />
        <select wire:model.lazy="id_plan" class="input-dark w-full sm:w-48 text-sm">
            <option value="">All Contracts</option>
            @foreach ($contracts as $contract)
            <option value="{{ $contract->id }}">{{ $contract->megabytes }} MB</option>
            @endforeach
        </select>
    </div>

    <!-- Table -->
    <div class="relative overflow-x-auto rounded-[8px] border border-dark-border" wire:loading.class="opacity-50">
        <table class="min-w-full">
            <thead class="bg-dark-elevated">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">IP Address</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Contract</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-4 py-3 text-right text-[11px] font-medium text-muted uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-border">
                @forelse ($clients as $client)
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
                            <div>
                                <div class="text-sm text-text-primary">{{ $client->nombre }} {{ $client->apellido }}</div>
                                <div class="text-xs text-muted">{{ $client->direccion }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-text-secondary font-mono">{{ $client->ip }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        @if ($client->contract)
                            <span class="text-sm text-text-secondary">{{ $client->contract->megabytes }} MB</span>
                        @else
                            <span class="text-sm text-muted">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        @if ($client->is_banned)
                            <x-ui-badge variant="banned">BANNED</x-ui-badge>
                        @else
                            <x-ui-badge variant="active">ACTIVE</x-ui-badge>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('clients.show', $client->id) }}" class="p-2 rounded-[6px] text-muted hover:bg-dark-elevated hover:text-text-primary" title="View">
                                <x-icon name="eye" class="w-4 h-4" />
                            </a>
                            <a href="{{ route('clients.edit', $client->id) }}" class="p-2 rounded-[6px] text-muted hover:bg-dark-elevated hover:text-text-primary" title="Edit">
                                <x-icon name="edit" class="w-4 h-4" />
                            </a>
                            @if ($client->is_banned)
                                <button wire:click="toggleBan({{ $client->id }})" class="p-2 rounded-[6px] text-muted hover:bg-success/10 hover:text-success" title="Unban">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                </button>
                            @else
                                <button wire:click="toggleBan({{ $client->id }})" class="p-2 rounded-[6px] text-muted hover:bg-warning/10 hover:text-warning" title="Ban">
                                    <x-icon name="ban" class="w-4 h-4" />
                                </button>
                            @endif
                            <button
                                type="button"
                                class="p-2 rounded-[6px] text-muted hover:bg-danger/10 hover:text-danger"
                                title="Delete"
                                onclick="window.confirmDelete('¿Eliminar cliente?', 'Esta acción no se puede deshacer.', '{{ route('clients.destroy', $client->id) }}', 'delete-client-{{ $client->id }}')"
                            >
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                        </div>
                        <form id="delete-client-{{ $client->id }}"
                            action="{{ route('clients.destroy', $client->id) }}"
                            method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-12">
                        <x-ui-empty-state
                            title="No clients found"
                            message="Try changing your search filters or create a new client."
                            icon="users"
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Loading Overlay -->
        <div wire:loading.delay wire:target="search, id_plan, previousPage, nextPage, gotoPage" class="absolute inset-0 z-10 flex items-center justify-center bg-dark-bg/60 backdrop-blur-sm rounded-[8px]">
            <div class="flex flex-col items-center gap-2">
                <div class="w-6 h-6 border-2 border-accent border-t-transparent rounded-full animate-spin"></div>
                <span class="text-[10px] text-muted uppercase tracking-wider">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <x-pagination :paginator="$clients" />
    </div>
</div>
