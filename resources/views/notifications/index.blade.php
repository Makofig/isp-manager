<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Notificaciones') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $unreadCount }} sin leer
                </p>
            </div>
            @if($unreadCount > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                        Marcar todas como leídas
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Notifications List -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @forelse($notifications as $notification)
                    <div class="px-6 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 {{ is_null($notification->read_at) ? 'bg-blue-50' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-4">
                                <!-- Icon -->
                                <div class="flex-shrink-0 mt-1">
                                    @if(str_contains($notification->type, 'QuotaGenerated'))
                                        <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif(str_contains($notification->type, 'PaymentOverdue'))
                                        <svg class="w-6 h-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    @elseif(str_contains($notification->type, 'ClientSuspended'))
                                        <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        @if(str_contains($notification->type, 'QuotaGenerated'))
                                            Cuota Mensual Generada
                                        @elseif(str_contains($notification->type, 'PaymentOverdue'))
                                            Pago Vencido
                                        @elseif(str_contains($notification->type, 'ClientSuspended'))
                                            Cliente Suspendido
                                        @else
                                            Notificación
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        @if(str_contains($notification->type, 'QuotaGenerated'))
                                            Se generó la cuota para {{ $notification->data['client_count'] ?? 0 }} clientes (período: {{ $notification->data['month_year'] ?? 'N/A' }}).
                                        @elseif(str_contains($notification->type, 'PaymentOverdue'))
                                            Cliente #{{ $notification->data['client_id'] ?? 'N/A' }} tiene un pago vencido de ${{ number_format($notification->data['amount'] ?? 0, 2) }} por {{ $notification->data['days_overdue'] ?? 0 }} días.
                                        @elseif(str_contains($notification->type, 'ClientSuspended'))
                                            Cliente #{{ $notification->data['client_id'] ?? 'N/A' }} suspendido: {{ $notification->data['reason'] ?? 'Sin razón' }}.
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400 mt-2">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                        &middot;
                                        {{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex-shrink-0">
                                @if(is_null($notification->read_at))
                                    <form action="{{ route('notifications.read', $notification) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            Marcar como leída
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">
                                        ✓ Leída
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <p class="mt-4 text-sm text-gray-500">Sin notificaciones</p>
                        <p class="text-xs text-gray-400 mt-1">Las notificaciones aparecerán aquí cuando ocurran eventos.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
