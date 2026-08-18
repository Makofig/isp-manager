<x-app-layout>
<x-page-header title="Admin" description="System settings and administration" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-medium text-muted uppercase tracking-wider">System</p>
                    <p class="mt-2 text-sm text-muted-light">Application settings</p>
                </div>
                <div class="w-10 h-10 rounded-[8px] bg-accent/10 flex items-center justify-center">
                    <x-icon name="settings" class="w-5 h-5 text-accent" />
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-medium text-muted uppercase tracking-wider">Users</p>
                    <p class="mt-2 text-sm text-muted-light">Manage admin users</p>
                </div>
                <div class="w-10 h-10 rounded-[8px] bg-success/10 flex items-center justify-center">
                    <x-icon name="users" class="w-5 h-5 text-success" />
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-medium text-muted uppercase tracking-wider">Security</p>
                    <p class="mt-2 text-sm text-muted-light">2FA, sessions, tokens</p>
                </div>
                <div class="w-10 h-10 rounded-[8px] bg-warning/10 flex items-center justify-center">
                    <x-icon name="settings" class="w-5 h-5 text-warning" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
