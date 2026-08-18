@props([
    'searchable' => true,
    'sortable' => true,
    'paginated' => false,
    'emptyTitle' => 'No results found',
    'emptyMessage' => null,
    'emptyIcon' => 'search',
])

<div
    x-data="{
        search: '',
        get filteredRows() {
            const rows = this.$root.querySelectorAll('[data-table-row]');
            if (!this.search) return rows;
            const q = this.search.toLowerCase();
            return Array.from(rows).filter(row => row.textContent.toLowerCase().includes(q));
        }
    }"
    class="space-y-4"
>
    @if($searchable)
    <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
        <x-search-input />
        {{ $toolbar ?? null }}
    </div>
    @endif

    <div class="relative overflow-x-auto rounded-[8px] border border-dark-border">
        <table class="min-w-full">
            <thead class="bg-dark-elevated">
                {{ $header }}
            </thead>
            <tbody class="divide-y divide-dark-border">
                {{ $slot }}
            </tbody>
        </table>

        <!-- Empty State -->
        <div x-show="filteredRows.length === 0" class="py-12">
            <x-ui-empty-state :title="$emptyTitle" :message="$emptyMessage" :icon="$emptyIcon" />
        </div>
    </div>
</div>
