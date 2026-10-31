@php
$navItems = [
    'main' => [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard'],
        ['route' => 'clients', 'label' => 'Clients', 'icon' => 'users'],
        ['route' => 'contracts', 'label' => 'Contracts', 'icon' => 'file-text'],
        ['route' => 'access-point', 'label' => 'Access Points', 'icon' => 'wifi'],
        ['route' => 'quota', 'label' => 'Quota', 'icon' => 'calendar'],
    ],
    'monitoring' => [
        ['route' => 'debtors', 'label' => 'Debtors', 'icon' => 'alert-triangle'],
        ['route' => 'clients.banned', 'label' => 'Banned', 'icon' => 'ban'],
        ['route' => 'statistics', 'label' => 'Statistics', 'icon' => 'bar-chart-2'],
    ],
    'system' => [
        ['route' => 'notifications.index', 'label' => 'Notifications', 'icon' => 'bell'],
        ['route' => 'admin', 'label' => 'Admin', 'icon' => 'settings'],
    ],
];

if (!function_exists('isActiveRoute')) {
    function isActiveRoute($route) {
        return request()->routeIs($route);
    }
}
@endphp

<aside
    x-data="{ collapsed: $store.sidebar.collapsed }"
    :class="$store.sidebar.collapsed ? 'w-[72px]' : 'w-[260px]'"
    class="fixed left-0 top-0 h-screen bg-dark-surface border-r border-dark-border z-40 flex flex-col transition-all duration-150"
>
    <!-- Logo -->
    <div class="h-16 flex items-center px-5 border-b border-dark-border">
        <div class="flex items-center gap-3 overflow-hidden">
            <div class="w-8 h-8 rounded-lg bg-accent/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div x-show="!$store.sidebar.collapsed" class="whitespace-nowrap">
                <div class="text-sm font-semibold text-text-primary tracking-wide">INTERSYS</div>
                <div class="text-[10px] text-muted tracking-widest uppercase">Network Management</div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-6">
        @foreach ($navItems as $section => $items)
            <div>
                <div x-show="!collapsed" class="px-3 mb-2">
                    <span class="text-[10px] font-medium text-muted uppercase tracking-wider">{{ $section }}</span>
                </div>
                <div class="space-y-1">
                    @foreach ($items as $item)
                        @php
                            $isActive = isActiveRoute($item['route']);
                        @endphp
                        <a
                            href="{{ route($item['route']) }}"
                            @if($isActive)
                                class="flex items-center gap-3 px-3 h-10 rounded-[8px] bg-accent/10 text-accent text-sm font-medium"
                            @else
                                class="flex items-center gap-3 px-3 h-10 rounded-[8px] text-muted-light text-sm hover:bg-dark-elevated hover:text-text-primary"
                            @endif
                            title="{{ $item['label'] }}"
                        >
                            <!-- Icon placeholder — replace with Lucide SVG -->
                            <span class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                <x-icon :name="$item['icon']" class="w-[18px] h-[18px]" />
                            </span>
                            <span x-show="!$store.sidebar.collapsed" class="truncate">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    <!-- Collapse toggle (desktop) -->
    <div class="hidden lg:block border-t border-dark-border p-3">
        <button
            @click="$store.sidebar.toggle()"
            class="flex items-center justify-center w-full h-9 rounded-[7px] text-muted hover:bg-dark-elevated hover:text-text-primary"
        >
            <svg x-show="!$store.sidebar.collapsed" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
            <svg x-show="$store.sidebar.collapsed" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    <!-- User info bottom -->
    <div x-show="!$store.sidebar.collapsed" class="border-t border-dark-border p-3">
        <div class="flex items-center gap-3 px-2">
            <div class="w-8 h-8 rounded-full bg-accent/20 flex items-center justify-center">
                <span class="text-xs font-medium text-accent">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-text-primary truncate">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="text-[10px] text-muted truncate">{{ auth()->user()->email ?? 'admin@intersys.com' }}</div>
            </div>
        </div>
    </div>
</aside>
