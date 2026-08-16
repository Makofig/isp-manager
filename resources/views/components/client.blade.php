<div class="container max-w-6xl">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Table Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Clients Members</h2>
                    <p class="text-gray-500 mt-1">Manage your clients members and their account permissions here.</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('clients.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                        Add Client
                    </a>
                </div>
            </div>
        </div>

        <!-- Table -->
        @livewire('clients-table')

    </div>
</div>
