<x-app-layout>
<x-page-header title="Create Client" description="Add a new client to the network">
    <a href="{{ route('clients') }}" class="btn-ghost text-sm">Cancel</a>
</x-page-header>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Error Summary -->
            @if ($errors->any())
            <div class="stat-card border-danger/30 bg-danger/5">
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

            <!-- Personal Information -->
            <div class="stat-card space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-text-primary">Personal Information</h3>
                    <p class="text-xs text-muted mt-0.5">Client's basic details</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-ui-input name="first_name" label="First Name" placeholder="Juan" :value="old('first_name')" :error="$errors->first('first_name')" />
                    <x-ui-input name="last_name" label="Last Name" placeholder="Pérez" :value="old('last_name')" :error="$errors->first('last_name')" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-ui-input type="email" name="email" label="Email" placeholder="juan@example.com" :value="old('email')" :error="$errors->first('email')" />
                    <x-ui-input type="tel" name="phone" label="Phone" placeholder="1234567890" :value="old('phone')" :error="$errors->first('phone')" />
                </div>

                <x-ui-input name="street_address" label="Street Address" placeholder="Calle 123" :value="old('street_address')" :error="$errors->first('street_address')" />
            </div>

            <!-- Network Information -->
            <div class="stat-card space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-text-primary">Network Information</h3>
                    <p class="text-xs text-muted mt-0.5">IP and access point assignment</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-ui-input name="ip_address" label="IP Address" placeholder="192.168.1.100" :value="old('ip_address')" :error="$errors->first('ip_address')" />
                    <x-ui-select name="access_point_id" label="Access Point" placeholder="Select access point" :error="$errors->first('access_point_id')">
                        @foreach ($points as $point)
                        <option value="{{ $point->id }}" {{ old('access_point_id') == $point->id ? 'selected' : '' }}>{{ $point->ssid }} ({{ $point->localidad }})</option>
                        @endforeach
                    </x-ui-select>
                </div>

                <x-ui-select name="contracts_id" label="Contract" placeholder="Select a contract" :error="$errors->first('contracts_id')">
                    @foreach ($contracts as $contract)
                    <option value="{{ $contract->id }}" {{ old('contracts_id') == $contract->id ? 'selected' : '' }}>{{ $contract->nombre }} — {{ $contract->megabytes }} MB (${{ number_format($contract->costo, 2) }})</option>
                    @endforeach
                </x-ui-select>
            </div>

            <!-- Photo Upload -->
            <div class="stat-card space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-text-primary">Client Photo</h3>
                    <p class="text-xs text-muted mt-0.5">Optional profile image</p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-[8px] bg-dark-elevated border border-dark-border flex items-center justify-center">
                        <x-icon name="users" class="w-6 h-6 text-muted" />
                    </div>
                    <div class="flex-1">
                        <input type="file" name="file_upload" id="file_upload" class="input-dark w-full text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-[6px] file:border-0 file:text-xs file:font-medium file:bg-accent/10 file:text-accent hover:file:bg-accent/20" />
                        @error('file_upload'))
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('clients') }}" class="btn-ghost text-sm">Cancel</a>
                <button type="submit" class="btn-primary text-sm">Create Client</button>
            </div>
        </form>
    </div>
</x-app-layout>
