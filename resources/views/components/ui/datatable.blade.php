@props([
    'searchable' => true,
    'sortable' => true,
    'paginated' => false,
    'emptyTitle' => 'Sin resultados',
    'emptyMessage' => null,
    'emptyIcon' => 'inbox',
])

<div
    x-data="{
        search: '',
        sortCol: '',
        sortDir: 'asc',
        get filteredRows() {
            let rows = this.$root.querySelectorAll('[data-table-row]');
            return Array.from(rows).filter(row => {
                if (!this.search) return true;
                const text = row.textContent.toLowerCase();
                return text.includes(this.search.toLowerCase());
            });
        },
        sort(col) {
            if (this.sortCol === col) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortCol = col;
                this.sortDir = 'asc';
            }
            // Emit event for Livewire to handle sorting
            this.$wire.set('sortColumn', col);
            this.$wire.set('sortDirection', this.sortDir);
        }
    }"
    class="space-y-4"
>
    @if($searchable)
    <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
        <div class="relative w-full sm:w-72">
            <input
                x-model="search"
                type="search"
                placeholder="Buscar..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
            >
            <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        </div>
        {{ $toolbar ?? null }}
    </div>
    @endif

    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                {{ $header }}
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    <div
        x-show="filteredRows.length === 0"
        class="py-12"
    >
        <x-ui-empty-state :title="$emptyTitle" :message="$emptyMessage" :icon="$emptyIcon" />
    </div>
</div>
