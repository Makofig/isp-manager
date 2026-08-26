<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contract') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container max-w-6xl">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="max-w-2xl mx-auto py-10">      
                        <form method="POST" action="{{ route('contracts.update', $contract->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="space-y-12">
                                <div class="border-b border-gray-900/10 pb-12">
                                    <h2 class="text-base/7 font-semibold text-gray-900">Create Contract</h2>
                                    <p class="mt-1 text-sm/6 text-gray-600">This information will be displayed publicly so be careful what you share.</p>
                                    @if ($errors->any())
                                        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>• {{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    @if (session('success'))
                                        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                                            {{ session('success') }}
                                        </div>
                                    @endif
                                   
                                </div>

                                <div class="border-b border-gray-900/10 pb-12">
                                    <h2 class="text-base/7 font-semibold text-gray-900">Information the Contract</h2>

                                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                        <div class="sm:col-span-3">
                                            <label for="name" class="block text-sm/6 font-medium text-gray-900">Name *</label>
                                            <div class="mt-2">
                                                <input id="name" type="text" name="name" value="{{ old('name', $contract->nombre) }}" autocomplete="given-name" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                @error('name')
                                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="megabytes" class="block text-sm/6 font-medium text-gray-900">Megabytes *</label>
                                            <div class="mt-2">
                                                <input id="megabytes" type="number" name="megabytes" value="{{ old('megabytes', $contract->megabytes) }}" min="1" autocomplete="off" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                @error('megabytes')
                                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="price" class="block text-sm/6 font-medium text-gray-900">Price *</label>
                                            <div class="mt-2">
                                                <input id="price" type="number" step="0.01" name="price" value="{{ old('price', $contract->costo) }}" placeholder="0.00" min="0" autocomplete="off" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                @error('price')
                                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex items-center justify-end gap-x-6">
                                <a href="{{ route('contracts')}}" type="button" class="rounded-md text-sm font-semibold px-3 py-2 text-gray-900 hover:bg-indigo-500 hover:text-white">Cancel</a>
                                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>