@props(['align' => 'right', 'width' => 48])

@php
$alignmentClasses = [
    'left' => 'left-0 origin-top-left',
    'right' => 'right-0 origin-top-right',
    'center' => 'left-1/2 -translate-x-1/2 origin-top',
];
$widthClasses = [48 => 'w-48', 56 => 'w-56', 64 => 'w-64'];
$alignClass = $alignmentClasses[$align] ?? $alignmentClasses['right'];
$widthClass = $widthClasses[$width] ?? 'w-48';
@endphp

<div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative inline-flex">
    {{ $trigger }}

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-2 {{ $widthClass }} rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 {{ $alignClass }}"
        style="display: none;"
        x-cloak
    >
        <div class="rounded-lg bg-white py-1">
            {{ $content }}
        </div>
    </div>
</div>
