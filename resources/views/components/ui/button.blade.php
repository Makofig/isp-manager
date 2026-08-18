@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
])

@php
$variants = [
    'primary' => 'btn-primary',
    'secondary' => 'btn-secondary',
    'danger' => 'btn-danger',
    'ghost' => 'btn-ghost',
];
$sizes = [
    'sm' => 'h-8 px-3 text-xs',
    'md' => 'h-[42px] px-4 text-sm',
    'lg' => 'h-12 px-6 text-base',
];
$class = ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $class]) }}
>
    {{ $slot }}
</button>
