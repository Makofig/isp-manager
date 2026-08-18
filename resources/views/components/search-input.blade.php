@props([
    'model' => 'search',
    'placeholder' => 'Search...',
    'debounce' => 300,
])

<div class="relative w-full max-w-sm">
    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none" />
    <input
        type="search"
        placeholder="{{ $placeholder }}"
        wire:model.live.debounce.{{ $debounce }}ms="{{ $model }}"
        class="input-dark w-full pl-10 pr-9 text-sm"
    />
    <button
        x-data
        @click="$wire.set('{{ $model }}', '')"
        class="absolute right-2 top-1/2 -translate-y-1/2 p-1 rounded text-muted hover:text-text-primary"
    >
        <x-icon name="x" class="w-3.5 h-3.5" />
    </button>
</div>
