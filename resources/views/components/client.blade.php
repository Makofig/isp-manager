@props(['onlyBanned' => false])

<x-page-header
    :title="$onlyBanned ? 'Banned Clients' : 'Clients'"
    :description="$onlyBanned ? 'Restricted client accounts' : 'Manage registered clients'"
>
    @if(!$onlyBanned)
        <a href="{{ route('clients.create') }}" class="btn-primary text-sm">+ New Client</a>
    @else
        <a href="{{ route('clients.export', ['type' => 'banned']) }}" class="btn-danger text-sm">Export PDF</a>
    @endif
</x-page-header>

@if($onlyBanned)
    @livewire('clients-table', ['onlyBanned' => true])
@else
    @livewire('clients-table')
@endif
