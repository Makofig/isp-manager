<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Clients Payments') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container max-w-6xl">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <!-- Table Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Details Payments - {{ optional($payment->cuota)->created_at ? $payment->cuota->created_at->format('M d, Y') : 'N/A' }}</h2>
                                <p class="text-gray-500 mt-1">View and manage information payment here.</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap py-10 px-2">
                        <img
                            class="h-32 w-32 rounded-full object-cover"
                            src="{{ $payment->clients->imagen
                                        ? asset('storage/clients/' . $payment->clients->imagen)
                                        : asset('images/default-avatar.png') }}"
                            alt="{{ $payment->clients->nombre }} {{ $payment->clients->apellido }}">
                        <div class="flex flex-col justify-center ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">{{ $payment->clients->nombre }} {{ $payment->clients->apellido }}</h3>
                            <p class="text-gray-500">Phone: {{ $payment->clients->telefono }}</p>
                            <p class="text-gray-500">IP: {{ $payment->clients->ip }}</p>
                            <p class="text-gray-500">{{ $payment->clients->direccion }}</p>
                        </div>
                    </div>
                    <div>
                        @include('payments.partials.payment-details', ['payment' => $payment])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>