<div class="relative" x-data="{ open: false }">
    <!-- Bell Button -->
    <button
        @click="open = !open"
        class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none"
        aria-label="Notificaciones"
    >
        <!-- Bell Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        <!-- Badge -->
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full transform translate-x-1/4 -translate-y-1/4">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div
        x-show="open"
        @click.outside="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50"
        style="display: none;"
    >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-700">Notificaciones</h3>
            @if($unreadCount > 0)
                <button
                    wire:click="markAllRead"
                    class="text-xs text-blue-600 hover:text-blue-800 font-medium"
                >
                    Marcar todas como leídas
                </button>
            @endif
        </div>

        <!-- Notification List -->
        <div class="max-h-80 overflow-y-auto">
            @forelse($notifications as $notification)
                <div
                    wire:click="markRead('{{ $notification->id }}')"
                    class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer {{ is_null($notification->read_at) ? 'bg-blue-50' : '' }}"
                >
                    <div class="flex items-start gap-3">
                        <!-- Icon based on type -->
                        <div class="flex-shrink-0 mt-0.5">
                            @if(str_contains($notification->type, 'QuotaGenerated'))
                                <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif(str_contains($notification->type, 'PaymentOverdue'))
                                <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            @elseif(str_contains($notification->type, 'ClientSuspended'))
                                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-700">
                                @if(str_contains($notification->type, 'QuotaGenerated'))
                                    Cuota generada para {{ $notification->data['client_count'] ?? 0 }} clientes ({{ $notification->data['month_year'] ?? '' }})
                                @elseif(str_contains($notification->type, 'PaymentOverdue'))
                                    Pago vencido: {{ $notification->data['days_overdue'] ?? 0 }} días, ${{ number_format($notification->data['amount'] ?? 0, 2) }}
                                @elseif(str_contains($notification->type, 'ClientSuspended'))
                                    Cliente suspendido: {{ $notification->data['reason'] ?? 'Sin razón' }}
                                @else
                                    Nueva notificación
                                @endif
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                            </p>
                        </div>

                        <!-- Unread indicator -->
                        @if(is_null($notification->read_at))
                            <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-6 text-center text-sm text-gray-500">
                    Sin notificaciones
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if(count($notifications) > 0)
            <div class="px-4 py-2 border-t border-gray-200 text-center">
                <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Ver todas
                </a>
            </div>
        @endif
    </div>
</div>
