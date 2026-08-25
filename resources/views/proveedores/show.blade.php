<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Provider Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">{{ $proveedor->nombre }}</h2>
                            <p class="text-gray-500 mt-1">{{ $proveedor->contacto }}</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('proveedores.edit', $proveedor) }}"
                               class="rounded-md bg-yellow-500 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-yellow-400">
                                Edit
                            </a>
                            <a href="{{ route('proveedores.index') }}"
                               class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 shadow hover:bg-gray-300">
                                Back
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Type</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($proveedor->tipo) }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Status</h4>
                            <p class="mt-1">
                                @if($proveedor->activo)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Upload / Download</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $proveedor->mb_up }} / {{ $proveedor->mb_down }} Mbps</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Total Bandwidth</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $proveedor->ancho_banda_total }} Mbps</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Monthly Price</h4>
                            <p class="mt-1 text-sm text-gray-900">${{ number_format($proveedor->precio_total, 2) }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Effective Cost per MB</h4>
                            <p class="mt-1 text-sm text-gray-900">${{ number_format($proveedor->costo_efectivo, 4) }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Phone</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $proveedor->telefono ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Email</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $proveedor->email ?? '-' }}</p>
                        </div>
                    </div>

                    @if($proveedor->direccion)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Address</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $proveedor->direccion }}</p>
                    </div>
                    @endif

                    @if($proveedor->notas)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Notes</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $proveedor->notas }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
