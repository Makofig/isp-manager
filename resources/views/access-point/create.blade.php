<x-app-layout>
    <x-page-header
        title="Create Access Point"
        description="Add a new network access point"
    />

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('access-point.store') }}" enctype="multipart/form-data" class="stat-card space-y-6">
            @csrf

            <!-- Error Summary -->
            @if ($errors->any())
            <div class="border border-danger/30 bg-danger/5 p-4 rounded-[8px]">
                <div class="flex items-start gap-3">
                    <x-icon name="alert-triangle" class="w-5 h-5 text-danger flex-shrink-0 mt-0.5" />
                    <div>
                        <p class="text-sm font-medium text-danger">Please fix the following errors:</p>
                        <ul class="mt-1 text-sm text-danger/80 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <!-- Information Section -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-text-primary">Access Point Information</h3>
                    <p class="text-xs text-muted mt-0.5">Configure the network details</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-ui-input name="ssid" label="SSID" placeholder="MiRed_WiFi" :value="old('ssid')" :error="$errors->first('ssid')" />
                    <x-ui-input name="frequency" label="Frequency" placeholder="2.4GHz" :value="old('frequency')" :error="$errors->first('frequency')" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-ui-input name="ip_address" label="IP Address" placeholder="192.168.0.1" :value="old('ip_address')" :error="$errors->first('ip_address')" />
                    <x-ui-input name="location" label="Location" placeholder="Centro" :value="old('location')" :error="$errors->first('location')" />
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-dark-border">
                <a href="{{ route('access-point') }}" class="btn-ghost text-sm">Cancel</a>
                <button type="submit" class="btn-primary text-sm">Create Access Point</button>
            </div>
        </form>
    </div>
</x-app-layout>
