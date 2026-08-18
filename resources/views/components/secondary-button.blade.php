<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-secondary text-xs']) }}>
    {{ $slot }}
</button>
