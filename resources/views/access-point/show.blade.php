<x-app-layout>
    <x-page-header
        title="Access Point"
        description="{{ $access_point->ssid }}"
    />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- AP Info Card -->
        <div class="lg:col-span-1">
            <div class="stat-card">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-[10px] bg-accent/10 flex items-center justify-center">
                        <x-icon name="wifi" class="w-6 h-6 text-accent" />
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-text-primary">{{ $access_point->ssid }}</h3>
                        <p class="text-xs text-muted">{{ $access_point->frecuencia }}</p>
                    </div>
                </div>

                <div class="space-y-3 pt-4 border-t border-dark-border">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted uppercase tracking-wider">IP Address</span>
                        <span class="text-sm text-text-secondary font-mono">{{ $access_point->ip_ap }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted uppercase tracking-wider">Location</span>
                        <span class="text-sm text-text-secondary">{{ $access_point->localidad }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted uppercase tracking-wider">Connected Clients</span>
                        <x-ui-badge variant="info">{{ $access_point->clients->count() }}</x-ui-badge>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted uppercase tracking-wider">Status</span>
                        <x-ui-badge variant="online">ONLINE</x-ui-badge>
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-2">
                    <a href="{{ route('access-point.edit', $access_point->id) }}" class="btn-secondary text-xs flex-1">Edit</a>
                    <a href="{{ route('access-point') }}" class="btn-ghost text-xs flex-1">Back</a>
                </div>
            </div>
        </div>

        <!-- Connected Clients Table -->
        <div class="lg:col-span-2">
            <x-page-header title="Connected Clients" description="Clients connected to this access point" />
            @livewire('access-clients-table', ['access_point_id' => $access_point->id])
        </div>
    </div>
</x-app-layout>
