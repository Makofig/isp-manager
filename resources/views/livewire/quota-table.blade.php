<div>
    <!-- Search -->
    <div class="mb-4">
        <x-search-input model="search" placeholder="Search quotas..." />
    </div>

    <!-- Table -->
    <div class="relative overflow-x-auto rounded-[8px] border border-dark-border">
        <table class="min-w-full">
            <thead class="bg-dark-elevated">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Quota</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Period</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-border">
                @forelse ($quotas as $quota)
                <tr class="hover:bg-dark-elevated/50 transition-colors duration-150" data-table-row>
                    <td class="px-4 py-3.5">
                        <div class="text-sm text-text-primary">Quota #{{ $quota->id }}</div>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-text-secondary">{{ $quota->created_at->format('F Y') }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <x-ui-badge variant="active">ACTIVE</x-ui-badge>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-12">
                        <x-ui-empty-state
                            title="No quotas found"
                            message="Generate monthly quotas to see them here."
                            icon="calendar"
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <x-pagination :paginator="$quotas" />
    </div>
</div>
