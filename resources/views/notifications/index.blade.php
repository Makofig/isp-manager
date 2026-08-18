<x-app-layout>
<x-page-header title="Notifications" description="{{ $unreadCount }} unread">
    @if($unreadCount > 0)
        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="btn-secondary text-sm">Mark all as read</button>
        </form>
    @endif
</x-page-header>

    <!-- Notifications List -->
    <div class="rounded-[8px] border border-dark-border overflow-hidden">
        @forelse($notifications as $notification)
            <div class="px-4 py-3.5 border-b border-dark-border last:border-0 hover:bg-dark-elevated/50 {{ is_null($notification->read_at) ? 'bg-accent/5' : '' }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0 mt-0.5">
                            @if(str_contains($notification->type, 'QuotaGenerated'))
                                <div class="w-8 h-8 rounded-[6px] bg-success/10 flex items-center justify-center">
                                    <x-icon name="check" class="w-4 h-4 text-success" />
                                </div>
                            @elseif(str_contains($notification->type, 'PaymentOverdue'))
                                <div class="w-8 h-8 rounded-[6px] bg-warning/10 flex items-center justify-center">
                                    <x-icon name="alert-triangle" class="w-4 h-4 text-warning" />
                                </div>
                            @elseif(str_contains($notification->type, 'ClientSuspended'))
                                <div class="w-8 h-8 rounded-[6px] bg-danger/10 flex items-center justify-center">
                                    <x-icon name="ban" class="w-4 h-4 text-danger" />
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-[6px] bg-dark-elevated flex items-center justify-center">
                                    <x-icon name="bell" class="w-4 h-4 text-muted" />
                                </div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div>
                            <p class="text-sm font-medium text-text-primary">
                                @if(str_contains($notification->type, 'QuotaGenerated'))
                                    Quota Generated
                                @elseif(str_contains($notification->type, 'PaymentOverdue'))
                                    Payment Overdue
                                @elseif(str_contains($notification->type, 'ClientSuspended'))
                                    Client Suspended
                                @else
                                    Notification
                                @endif
                            </p>
                            <p class="text-sm text-muted-light mt-0.5">
                                @if(str_contains($notification->type, 'QuotaGenerated'))
                                    Generated quota for {{ $notification->data['client_count'] ?? 0 }} clients ({{ $notification->data['month_year'] ?? 'N/A' }}).
                                @elseif(str_contains($notification->type, 'PaymentOverdue'))
                                    Client #{{ $notification->data['client_id'] ?? 'N/A' }} has an overdue payment of ${{ number_format($notification->data['amount'] ?? 0, 2) }} ({{ $notification->data['days_overdue'] ?? 0 }} days).
                                @elseif(str_contains($notification->type, 'ClientSuspended'))
                                    Client #{{ $notification->data['client_id'] ?? 'N/A' }} suspended: {{ $notification->data['reason'] ?? 'No reason' }}.
                                @endif
                            </p>
                            <p class="text-xs text-muted mt-1">
                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex-shrink-0">
                        @if(is_null($notification->read_at))
                            <form action="{{ route('notifications.read', $notification) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs text-accent hover:text-accent-hover font-medium">
                                    Mark read
                                </button>
                            </form>
                        @else
                            <span class="text-xs text-muted">Read</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="px-4 py-12">
                <x-ui-empty-state
                    title="No notifications"
                    message="Notifications will appear here when events occur."
                    icon="bell"
                />
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</x-app-layout>
