@props(['lines' => 3, 'widths' => null])

@php
$widths = $widths ?? ['w-full', 'w-4/5', 'w-3/5', 'w-2/3', 'w-1/2'];
@endphp

<div class="space-y-3 animate-pulse" role="status" aria-label="Cargando...">
    @for ($i = 0; $i < $lines; $i++)
        <div class="h-4 bg-dark-elevated rounded {{ $widths[$i % count($widths)] }}"></div>
    @endfor
    <span class="sr-only">Cargando...</span>
</div>
