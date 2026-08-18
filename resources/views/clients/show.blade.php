<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Clients Show') }}
        </h2>
    </x-slot>
    @if (session('success'))
    <script>
        window.toast.success(`{{ session('success') }}`);
    </script>
    @endif
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container max-w-6xl">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <!-- Table Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Client Details</h2>
                                <p class="text-gray-500 mt-1">View and manage client information here.</p>
                            </div>
                            <x-button-previous></x-button-previous>
                        </div>
                    </div>
                    <div class="flex flex-wrap py-10 px-2">
                        <img
                            class="h-32 w-32 rounded-full object-cover"
                            src="{{ $client->imagen 
                                        ? asset('storage/clients/' . $client->imagen) 
                                        : asset('images/default-avatar.png') }}"
                            alt="{{ $client->nombre }} {{ $client->apellido }}">
                        <div class="flex flex-col justify-center ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">{{ $client->nombre }} {{ $client->apellido }}</h3>
                            <p class="text-gray-500">{{ $client->email }}</p>
                            <p class="text-gray-500">Phone: {{ $client->telefono }}</p>
                            <p class="text-gray-500">{{ $client->ip }}</p>
                            <p class="text-gray-500">{{ $client->direccion }}</p>

                        </div>
                    </div>
                    <div>
                        @livewire('payments-table', ['clientId' => $client->id])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>