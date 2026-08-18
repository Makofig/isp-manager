<x-page-header title="Quotas" description="Monthly fee management">
    <a href="{{ route('quota.create') }}" class="btn-primary text-sm" wire:navigate>+ Generate Quota</a>
</x-page-header>

@livewire('quota-table')
