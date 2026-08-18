@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-[11px] font-medium text-muted uppercase tracking-wider']) }}>
    {{ $value ?? $slot }}
</label>
