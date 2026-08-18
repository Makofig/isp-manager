@props(['size' => 'md', 'text' => null])

@php
$sizes = [
    'sm' => 'w-4 h-4 border-2',
    'md' => 'w-8 h-8 border-3',
    'lg' => 'w-12 h-12 border-4',
];
$sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="flex flex-col items-center justify-center gap-3" role="status" aria-label="Cargando">
    <svg class="animate-spin text-blue-600 {{ $sizeClass }}" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
    </svg>
    @if($text)
        <p class="text-sm text-gray-500" x-text="@js($text)">{{ $text }}</p>
    @endif
</div>
