<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Clients Show') }}
        </h2>
    </x-slot>
    @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Exito!',
            text: `{{ session('success') }}`,
            timer: 2000,
            showConfirmButton: false
        })
    </script>
    @endif
    <script>
        function retryPayment(clientId) {
            Swal.fire({
                title: 'Retry missing payments?',
                text: "This will create missing payments for this client in the current quota.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, retry!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("payments.retry") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ client_id: clientId })
                    })
                    .then(r => r.json())
                    .then(data => {
                        Swal.fire({
                            icon: data.error ? 'error' : 'success',
                            title: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    });
                }
            });
        }
    </script>
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
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">Payment History</h3>
                            <div class="flex gap-2">
                                <button onclick="retryPayment({{ $client->id }})"
                                        class="px-3 py-1.5 text-sm bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">
                                    Retry Missing Payments
                                </button>
                                <a href="{{ route('payments.export.pdf', ['status' => 'all']) }}"
                                   class="px-3 py-1.5 text-sm bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200">
                                    Export PDF
                                </a>
                            </div>
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