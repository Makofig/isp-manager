@props([
    'title' => '',
    'description' => null,
])

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-lg font-semibold text-text-primary">{{ $title }}</h1>
        @if($description)
            <p class="text-sm text-muted mt-1">{{ $description }}</p>
        @endif
    </div>
    @if($slot->isNotEmpty())
        <div class="flex items-center gap-2 flex-shrink-0">
            {{ $slot }}
        </div>
    @endif
</div>
