<div>
    <div class="relative overflow-x-auto rounded-[8px] border border-dark-border">
        <table class="min-w-full">
            <thead class="bg-dark-elevated">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">IP Address</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Contract</th>
                    <th scope="col" class="px-4 py-3 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-border">
                @forelse ($clients as $client)
                <tr class="hover:bg-dark-elevated/50 transition-colors duration-150">
                    <td class="px-4 py-3.5">
                        <div class="text-sm text-text-primary">{{ $client->nombre }} {{ $client->apellido }}</div>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-text-secondary font-mono">{{ $client->ip }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-text-secondary">{{ $client->contract->megabytes ?? '—' }} MB</span>
                    </td>
                    <td class="px-4 py-3.5">
                        @if ($client->is_banned)
                            <x-ui-badge variant="banned">BANNED</x-ui-badge>
                        @else
                            <x-ui-badge variant="active">ACTIVE</x-ui-badge>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-12">
                        <x-ui-empty-state title="No clients connected" description="This access point has no clients connected." icon="wifi" />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="mt-4">
        <x-pagination :paginator="$clients" />
    </div>
</div>
