<div>
    @if(session('success'))
    <script>window.toast.success(`{{ session('success') }}`);</script>
    @endif
    @if(session('error'))
    <script>window.toast.error(`{{ session('error') }}`);</script>
    @endif

    <!-- Search -->
    <div class="mb-4">
        <x-search-input model="search" placeholder="Search contracts..." />
    </div>

    <!-- Table -->
    <div class="relative overflow-x-auto rounded-[8px] border border-dark-border">
        <table class="min-w-full">
            <thead class="bg-dark-elevated">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Bandwidth</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Price</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Clients</th>
                    <th scope="col" class="px-4 py-3 text-right text-[11px] font-medium text-muted uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-border">
                @forelse ($contracts as $contract)
                <tr class="hover:bg-dark-elevated/50 transition-colors duration-150" data-table-row>
                    <td class="px-4 py-3.5">
                        <div class="text-sm text-text-primary">{{ $contract->nombre }}</div>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-text-secondary">{{ $contract->megabytes }} MB</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-text-secondary">${{ number_format($contract->costo, 2, ',', '.') }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <x-ui-badge variant="info">{{ $contract->clients->count() }}</x-ui-badge>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('contracts.edit', $contract->id) }}" class="p-2 rounded-[6px] text-muted hover:bg-dark-elevated hover:text-text-primary" title="Edit">
                                <x-icon name="edit" class="w-4 h-4" />
                            </a>
                            <button
                                type="button"
                                class="p-2 rounded-[6px] text-muted hover:bg-danger/10 hover:text-danger"
                                title="Delete"
                                onclick="window.confirmDelete('¿Eliminar contrato?', 'Esta acción no se puede deshacer.', '{{ route('contracts.destroy', $contract->id) }}', 'delete-contract-{{ $contract->id }}')"
                            >
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                        </div>
                        <form id="delete-contract-{{ $contract->id }}"
                            action="{{ route('contracts.destroy', $contract->id) }}"
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
                            title="No contracts found"
                            message="Create your first internet plan to get started."
                            icon="file-text"
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <x-pagination :paginator="$contracts" />
    </div>
</div>
