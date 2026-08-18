@props(['maxWidth' => '2xl', 'closeable' => true])

@php
$maxWidthClasses = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    'full' => 'max-w-full mx-4',
];
$widthClass = $maxWidthClasses[$maxWidth] ?? $maxWidthClasses['2xl'];
@endphp

<div x-data="{ open: false }" @keydown.escape.window="open = false" class="inline-flex">
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
        >
            <div
                @click.outside="$wire.open = open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-dark-surface border border-dark-border rounded-[10px] shadow-2xl {{ $widthClass }} w-full max-h-[90vh] overflow-y-auto"
            >
                @if($closeable)
                <button
                    @click="open = false"
                    class="absolute top-4 right-4 text-muted hover:text-text-primary transition rounded-[7px] p-1 hover:bg-dark-elevated focus:outline-none focus:ring-2 focus:ring-dark-border"
                    aria-label="Cerrar"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                @endif

                {{ $slot }}
            </div>
        </div>
    </template>
</div>
