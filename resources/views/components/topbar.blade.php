@props(['title' => null])

@php
$unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
@endphp

<header class="sticky top-0 z-30 h-16 bg-dark-surface border-b border-dark-border flex items-center justify-between px-6">
    <!-- Left: Mobile menu toggle + Search -->
    <div class="flex items-center gap-4 flex-1">
        <!-- Mobile hamburger -->
        <button
            class="lg:hidden p-2 rounded-[7px] text-muted hover:bg-dark-elevated hover:text-text-primary"
            @click="$dispatch('toggle-sidebar')"
        >
            <x-icon name="menu" class="w-5 h-5" />
        </button>

        <!-- Search -->
        <div class="relative w-full max-w-md hidden sm:block">
            <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted" />
            <input
                type="search"
                placeholder="Search clients, contracts, IPs..."
                class="input-dark w-full pl-10 pr-4 text-sm"
            >
        </div>
    </div>

    <!-- Right: Notifications + User -->
    <div class="flex items-center gap-3">
        <!-- Notifications Bell -->
        <a
            href="{{ route('notifications.index') }}"
            class="relative p-2 rounded-[7px] text-muted hover:bg-dark-elevated hover:text-text-primary"
        >
            <x-icon name="bell" class="w-5 h-5" />
            @if($unreadCount > 0)
                <span class="absolute top-1 right-1 w-2 h-2 bg-danger rounded-full"></span>
            @endif
        </a>

        <!-- User Menu -->
        <div x-data="{ open: false }" class="relative">
            <button
                @click="open = !open"
                class="flex items-center gap-2 p-1.5 rounded-[7px] hover:bg-dark-elevated"
            >
                <div class="w-8 h-8 rounded-full bg-accent/20 flex items-center justify-center">
                    <span class="text-xs font-medium text-accent">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                </div>
                <span class="hidden md:block text-sm text-text-primary max-w-[120px] truncate">
                    {{ auth()->user()->name ?? 'Admin' }}
                </span>
                <svg class="w-4 h-4 text-muted hidden md:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown -->
            <div
                x-show="open"
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="absolute right-0 mt-2 w-56 bg-dark-surface border border-dark-border rounded-[8px] shadow-lg py-1 z-50"
                style="display: none;"
            >
                <div class="px-4 py-3 border-b border-dark-border">
                    <div class="text-sm font-medium text-text-primary">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="text-xs text-muted truncate">{{ auth()->user()->email ?? 'admin@intersys.com' }}</div>
                </div>
                <div class="py-1">
                    <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 h-9 text-sm text-muted-light hover:bg-dark-elevated hover:text-text-primary">
                        <x-icon name="users" class="w-4 h-4" />
                        Profile
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 h-9 text-sm text-muted-light hover:bg-dark-elevated hover:text-text-primary">
                        <x-icon name="settings" class="w-4 h-4" />
                        Settings
                    </a>
                </div>
                <div class="border-t border-dark-border py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 h-9 text-sm text-muted-light hover:bg-dark-elevated hover:text-text-primary text-left">
                            <x-icon name="logout" class="w-4 h-4" />
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
