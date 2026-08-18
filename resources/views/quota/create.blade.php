<x-app-layout>
    <x-page-header
        title="Generate Quota"
        description="Create monthly fees for all clients"
    />

    <div class="max-w-2xl">
        <form method="POST" id="create-form" action="{{ route('quota.store') }}" enctype="multipart/form-data" class="stat-card space-y-6">
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
                    <h3 class="text-sm font-medium text-text-primary">Quota Period</h3>
                    <p class="text-xs text-muted mt-0.5">Select the month and year for quota generation</p>
                </div>

                <x-ui-input name="months_year" label="Period" :value="Carbon\Carbon::now()->format('F Y')" disabled />
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-dark-border">
                <a href="{{ route('quota') }}" class="btn-ghost text-sm">Cancel</a>
                <button type="submit" class="btn-primary text-sm">Generate Quota</button>
            </div>
        </form>
    </div>
</x-app-layout>
