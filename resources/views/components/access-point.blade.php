<x-page-header title="Access Points" description="Manage network access points">
    <a href="{{ route('access-point.create') }}" class="btn-primary text-sm">+ New Access Point</a>
</x-page-header>

@livewire('access-table')
