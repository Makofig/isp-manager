@props([
    'type' => 'text',
    'placeholder' => '',
    'label' => null,
    'error' => null,
    'disabled' => false,
])

<div class="space-y-1.5">
    @if($label)
        <label class="block text-[11px] font-medium text-muted uppercase tracking-wider">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        placeholder="{{ $placeholder }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => 'input-dark w-full text-sm ' . ($error ? 'border-danger' : '')]) }}
    />
    @if($error)
        <p class="text-xs text-danger">{{ $error }}</p>
    @endif
</div>
