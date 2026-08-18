@props(['variant' => 'default'])

@php
$variants = [
    'default' => 'bg-dark-elevated text-muted-light border border-dark-border',
    'success' => 'bg-success/10 text-success border border-success/20',
    'warning' => 'bg-warning/10 text-warning border border-warning/20',
    'danger'  => 'bg-danger/10 text-danger border border-danger/20',
    'info'    => 'bg-accent/10 text-accent border border-accent/20',
    'active'  => 'bg-success/10 text-success border border-success/20',
    'banned'  => 'bg-danger/10 text-danger border border-danger/20',
    'debtor'  => 'bg-warning/10 text-warning border border-warning/20',
    'inactive'=> 'bg-dark-elevated text-muted border border-dark-border',
    'online'  => 'bg-success/10 text-success border border-success/20',
    'offline' => 'bg-dark-elevated text-muted border border-dark-border',
];
$variantClass = $variants[$variant] ?? $variants['default'];
@endphp

<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium uppercase tracking-wider {{ $variantClass }}">
    {{ $slot }}
</span>
