<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-danger text-xs']) }}>
    {{ $slot }}
</button>
