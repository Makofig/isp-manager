<x-app-layout>
    <x-page-header title="Banned Clients" description="Restricted client accounts">
        <a href="{{ route('clients.export', ['type' => 'banned']) }}" class="btn-danger text-sm">Export PDF</a>
    </x-page-header>

    @livewire('clients-table', ['onlyBanned' => true])
</x-app-layout>
