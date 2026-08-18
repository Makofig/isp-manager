<x-page-header title="Contracts" description="Manage available plans">
    <a href="{{ route('contracts.create') }}" class="btn-primary text-sm">+ New Contract</a>
</x-page-header>

@livewire('contracts-table')
