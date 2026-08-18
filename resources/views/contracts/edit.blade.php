<x-app-layout>
    <x-page-header
        title="Edit Contract"
        description="{{ $contract->nombre }}"
    />

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('contracts.update', $contract->id) }}" enctype="multipart/form-data" class="stat-card space-y-6">
            @csrf
            @method('PUT')

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
                    <h3 class="text-sm font-medium text-text-primary">Contract Information</h3>
                    <p class="text-xs text-muted mt-0.5">Update the plan details</p>
                </div>

                <x-ui-input name="name" label="Plan Name" placeholder="Plan Premium" :value="old('name', $contract->nombre)" :error="$errors->first('name')" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-ui-input type="number" name="megabytes" label="Bandwidth (MB)" placeholder="100" :value="old('megabytes', $contract->megabytes)" :error="$errors->first('megabytes')" />
                    <x-ui-input type="text" name="price" label="Price" placeholder="299.99" :value="old('price', $contract->costo)" :error="$errors->first('price')" />
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-dark-border">
                <a href="{{ route('contracts') }}" class="btn-ghost text-sm">Cancel</a>
                <button type="submit" class="btn-primary text-sm">Save Changes</button>
            </div>
        </form>
    </div>
</x-app-layout>
