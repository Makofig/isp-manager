<div>
    @if(session('success'))
    <script>window.toast.success(`{{ session('success') }}`);</script>
    @endif
    @if(session('error'))
    <script>window.toast.error(`{{ session('error') }}`);</script>
    @endif

    <!-- Search -->
    <div class="mb-4">
        <x-search-input model="search" placeholder="Search access points..." />
    </div>

    <!-- Table -->
    <div class="relative overflow-x-auto rounded-[8px] border border-dark-border">
        <table class="min-w-full">
            <thead class="bg-dark-elevated">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">SSID</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">IP Address</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Location</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Clients</th>
                    <th scope="col" class="px-4 py-3 text-right text-[11px] font-medium text-muted uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-border">
                @forelse ($points as $point)
                <tr class="hover:bg-dark-elevated/50 transition-colors duration-150" data-table-row>
                    <td class="px-4 py-3.5">
                        <div class="text-sm text-text-primary">{{ $point->ssid }}</div>
                        <div class="text-xs text-muted">{{ $point->frecuencia }}</div>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-text-secondary font-mono">{{ $point->ip_ap }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-text-secondary">{{ $point->localidad }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <x-ui-badge variant="info">{{ $point->clients->count() }}</x-ui-badge>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('access-point.show', $point->id) }}" class="p-2 rounded-[6px] text-muted hover:bg-dark-elevated hover:text-text-primary" title="View">
                                <x-icon name="eye" class="w-4 h-4" />
                            </a>
                            <a href="{{ route('access-point.edit', $point->id) }}" class="p-2 rounded-[6px] text-muted hover:bg-dark-elevated hover:text-text-primary" title="Edit">
                                <x-icon name="edit" class="w-4 h-4" />
                            </a>
                            <button
                                type="button"
                                class="p-2 rounded-[6px] text-muted hover:bg-danger/10 hover:text-danger"
                                title="Delete"
                                onclick="window.confirmDelete('¿Eliminar punto de acceso?', 'Esta acción no se puede deshacer.', '{{ route('access-point.destroy', $point->id) }}', 'delete-points-{{ $point->id }}')"
                            >
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                        </div>
                        <form id="delete-points-{{ $point->id }}"
                            action="{{ route('access-point.destroy', $point->id) }}"
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
                            title="No access points found"
                            message="Add your first access point to start managing your network."
                            icon="wifi"
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <x-pagination :paginator="$points" />
    </div>
</div>
