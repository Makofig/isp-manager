<div>
    <x-page-header
        title="Generate Quota"
        description="Create monthly fees for all clients"
    />

    <div class="max-w-2xl">
        <form wire:submit.prevent="save" class="stat-card space-y-6">
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
                    <p class="text-xs text-muted mt-0.5">The quota will be generated for the current month</p>
                </div>

                <x-ui-input name="months_year" label="Period" :value="$months_year" disabled />
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-dark-border">
                <a href="{{ route('quota') }}" class="btn-ghost text-sm">Cancel</a>
                <button type="button" wire:click="confirmCreate" wire:loading.attr="disabled" class="btn-primary text-sm">
                    Generate Quota
                </button>
            </div>
        </form>

        <!-- Loading Overlay -->
        @if($loading)
        <div wire:poll.300ms="checkProgress" class="fixed inset-0 z-50 flex items-center justify-center bg-dark-bg/80 backdrop-blur-sm">
            <div class="stat-card w-80 text-center">
                @if($status === 'processing')
                    <h3 class="text-sm font-medium text-text-primary mb-4">Generating Fees...</h3>
                    <div class="w-full bg-dark-elevated rounded-full h-3">
                        <div class="bg-accent h-3 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                    </div>
                    <p class="mt-2 text-sm text-muted">{{ $progress }}%</p>
                @endif

                @if($status === 'success')
                    <div class="text-success text-4xl mb-2">&#10003;</div>
                    <h3 class="text-sm font-medium text-text-primary">Quota generated successfully</h3>
                @endif
            </div>
        </div>
        @endif
    </div>

    <script>
        Livewire.on('show-confirm', () => {
            window.confirmAction(
                '¿Generar cuotas?',
                'Esta acción creará las cuotas para el mes actual.',
                () => Livewire.dispatch('execute-save')
            );
        })

        Livewire.on('quota-finished', () => {
            setTimeout(() => {
                @this.loading = false
                @this.status = 'processing'
            }, 1000)
        })

        Livewire.on('execute-save', () => {
            @this.save()
        })
    </script>
</div>
