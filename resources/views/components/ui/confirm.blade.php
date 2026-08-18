@props([
    'title' => '¿Estás seguro?',
    'message' => 'Esta acción no se puede deshacer.',
    'confirmText' => 'Confirmar',
    'cancelText' => 'Cancelar',
    'variant' => 'danger',
    'confirmAction' => null,
    'confirmId' => null,
])

@php
$variants = [
    'danger' => 'bg-danger hover:opacity-90 focus:ring-danger/50',
    'warning' => 'bg-warning hover:opacity-90 focus:ring-warning/50',
    'info' => 'bg-accent hover:bg-accent-hover focus:ring-accent/50',
];
$btnClass = $variants[$variant] ?? $variants['danger'];
@endphp

<div
    x-data="{
        open: false,
        loading: false,
        title: @js($title),
        message: @js($message),
        confirmText: @js($confirmText),
        cancelText: @js($cancelText),
        action: @js($confirmAction),
        id: @js($confirmId),
        confirm() {
            this.loading = true;
            if (this.action) {
                @this.call(this.action, this.id).then(() => {
                    this.open = false;
                    this.loading = false;
                }).catch(() => {
                    this.loading = false;
                });
            }
        }
    }"
    @keydown.escape.window="open = false"
    class="inline-flex"
>
    {{ $trigger }}

    <template x-teleport="body">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
            style="display: none;"
            role="dialog"
            aria-modal="true"
            :aria-label="title"
        >
            <div
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-dark-surface border border-dark-border rounded-[10px] shadow-2xl max-w-md w-full p-6"
            >
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                         :class="{
                             'bg-danger/10': '{{ $variant }}' === 'danger',
                             'bg-warning/10': '{{ $variant }}' === 'warning',
                             'bg-accent/10': '{{ $variant }}' === 'info'
                         }">
                        <svg class="w-6 h-6"
                             :class="{
                                 'text-danger': '{{ $variant }}' === 'danger',
                                 'text-warning': '{{ $variant }}' === 'warning',
                                 'text-accent': '{{ $variant }}' === 'info'
                             }"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-text-primary" x-text="title"></h3>
                        <p class="mt-1 text-sm text-muted-light" x-text="message"></p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        @click="open = false"
                        type="button"
                        class="btn-secondary text-sm"
                    >
                        <span x-text="cancelText"></span>
                    </button>
                    <button
                        @click="confirm"
                        :disabled="loading"
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-white rounded-[7px] focus:outline-none focus:ring-2 transition disabled:opacity-50 disabled:cursor-not-allowed {{ $btnClass }}"
                    >
                        <span x-show="!loading" x-text="confirmText"></span>
                        <span x-show="loading" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Procesando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
