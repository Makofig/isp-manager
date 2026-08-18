<!-- Pagination -->
<div class="flex items-center justify-between flex-col sm:flex-row gap-3">
    <div>
        @if ($paginator->hasPages())
            <span class="text-xs text-muted">
                Showing <span class="text-text-secondary">{{ $paginator->firstItem() }}</span> to <span class="text-text-secondary">{{ $paginator->lastItem() }}</span> of <span class="text-text-secondary">{{ $paginator->total() }}</span> results
            </span>
        @endif
    </div>
    <div>
        @if ($paginator->hasPages())
        <nav class="flex items-center gap-1" aria-label="Pagination">
            @if ($paginator->onFirstPage())
                <span class="p-2 rounded-[6px] text-muted cursor-not-allowed">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </span>
            @else
                <a href="#" wire:click.prevent="previousPage" class="p-2 rounded-[6px] text-muted-light hover:bg-dark-elevated hover:text-text-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
            @endif

            @foreach ($paginator->links()->elements[0] ?? [] as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="px-3 py-1.5 rounded-[6px] bg-accent/10 text-accent text-sm font-medium">
                        {{ $page }}
                    </span>
                @else
                    <a href="#" wire:click.prevent="gotoPage({{ $page }})" class="px-3 py-1.5 rounded-[6px] text-muted-light text-sm hover:bg-dark-elevated hover:text-text-primary">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="#" wire:click.prevent="nextPage" class="p-2 rounded-[6px] text-muted-light hover:bg-dark-elevated hover:text-text-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </a>
            @else
                <span class="p-2 rounded-[6px] text-muted cursor-not-allowed">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </span>
            @endif
        </nav>
        @endif
    </div>
</div>
