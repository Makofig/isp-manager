@props([
    'placeholder' => 'Select an option',
    'label' => null,
    'error' => null,
])

<div class="space-y-1.5">
    @if($label)
        <label class="block text-[11px] font-medium text-muted uppercase tracking-wider">{{ $label }}</label>
    @endif
    <select
        {{ $attributes->merge(['class' => 'input-dark w-full text-sm appearance-none bg-no-repeat bg-[right_0.75rem_center] ' . ($error ? 'border-danger' : '')]) }}
        style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 24 24%27 stroke=%27%235D6670%27 stroke-width=%272%27%3E%3Cpath stroke-linecap=%27round%27 stroke-linejoin=%27round%27 d=%27M19 9l-7 7-7-7%27/%3E%3C/svg%3E'); background-size: 1rem;"
    >
        <option value="" disabled>{{ $placeholder }}</option>
        {{ $slot }}
    </select>
    @if($error)
        <p class="text-xs text-danger">{{ $error }}</p>
    @endif
</div>
