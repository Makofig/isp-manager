@props(['target' => null])

<div
    wire:loading.flex
    @if($target) wire:target="{{ $target }}" @endif
    class="absolute inset-0 z-20 flex items-center justify-center bg-dark-bg/80 backdrop-blur-sm rounded-[8px]"
    style="display: none;"
>
    <div class="flex flex-col items-center gap-3">
        <div class="w-8 h-8 border-2 border-accent border-t-transparent rounded-full animate-spin"></div>
        <span class="text-xs text-muted uppercase tracking-wider">Loading...</span>
    </div>
</div>
