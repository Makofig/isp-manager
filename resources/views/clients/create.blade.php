<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Clients') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container max-w-6xl">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="max-w-2xl mx-auto py-10">      
                        <form method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-12">
                                <div class="border-b border-gray-900/10 pb-12">
                                    <h2 class="text-base/7 font-semibold text-gray-900">Create Client</h2>
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
                                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                        <div class="col-span-full">
                                            <div class="mt-2 flex flex-col items-center gap-x-3">
                                                <svg viewBox="0 0 24 24" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-12 text-gray-300">
                                                    <path d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" clip-rule="evenodd" fill-rule="evenodd" />
                                                </svg>
                                                
                                            </div>
                                        </div>

                                        <div class="col-span-full">
                                            <label for="cover-photo" class="block text-sm/6 font-medium text-gray-900">Cover photo</label>
                                            <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10">
                                                <div class="text-center">
                                                    <svg viewBox="0 0 24 24" fill="currentColor" data-slot="icon" aria-hidden="true" class="mx-auto size-12 text-gray-300">
                                                        <path d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clip-rule="evenodd" fill-rule="evenodd" />
                                                    </svg>
                                                    <div class="mt-4 flex text-sm/6 text-gray-600">
                                                        <label for="file_upload" class="relative cursor-pointer rounded-md bg-transparent font-semibold text-indigo-600 focus-within:outline-2 focus-within:outline-offset-2 focus-within:outline-indigo-600 hover:text-indigo-500">
                                                            <span>Upload a file</span>
                                                            <input id="file_upload" type="file" name="file_upload" class="sr-only" />
                                                        </label>
                                                        <p class="pl-1">or drag and drop</p>
                                                    </div>
                                                    <p class="text-xs/5 text-gray-600">PNG, JPG, GIF up to 10MB</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-b border-gray-900/10 pb-12">
                                    <h2 class="text-base/7 font-semibold text-gray-900">Personal Information</h2>
                                    <p class="mt-1 text-sm/6 text-gray-600">Use a permanent address where you can receive mail.</p>

                                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                        <div class="sm:col-span-3">
                                            <label for="first_name" class="block text-sm/6 font-medium text-gray-900">First name</label>
                                            <div class="mt-2">
                                                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" autocomplete="given-name" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                @error('first_name')
                                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="last_name" class="block text-sm/6 font-medium text-gray-900">Last name</label>
                                            <div class="mt-2">
                                                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" autocomplete="family-name" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                @error('last_name')
                                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="sm:col-span-4">
                                            <label for="email" class="block text-sm/6 font-medium text-gray-900">Email address</label>
                                            <div class="mt-2">
                                                <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                @error('email')
                                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="sm:col-span-4">
                                            <label for="phone" class="block text-sm/6 font-medium text-gray-900">Phone</label>
                                            <div class="mt-2">
                                                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                @error('phone')
                                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="ip_address" class="block text-sm/6 font-medium text-gray-900">IP Address</label>
                                            <div class="mt-2">
                                                <input id="ip_address" type="text" name="ip_address" value="{{ old('ip_address') }}" autocomplete="ip_address" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                @error('ip_address')
                                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="contracts_id" class="block text-sm/6 font-medium text-gray-900">Contracts</label>
                                            <div class="mt-2 grid grid-cols-1">
                                                <select id="contracts_id" name="contracts_id" autocomplete="contracts_id-name" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                    <option>Select a contract</option>
                                                    @foreach ($contracts as $contract)
                                                        <option value="{{ $contract->id }}" {{ old('contracts_id') == $contract->id ? 'selected' : '' }}>{{ $contract->megabytes }} - {{ $contract->nombre }} (${{ number_format($contract->costo, 2) }})</option>
                                                    @endforeach
                                                </select>
                                                @error('contracts_id')
                                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                                @enderror
                                                <svg viewBox="0 0 16 16" fill="currentColor" data-slot="icon" aria-hidden="true" class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4">
                                                    <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="access_point_id" class="block text-sm/6 font-medium text-gray-900">Access Point</label>
                                            <div class="mt-2 grid grid-cols-1">
                                                <select id="access_point_id" name="access_point_id" autocomplete="access_point_id-name" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                    <option>Select a point</option>
                                                    @foreach ($points as $point)
                                                        <option value="{{ $point->id }}" {{ old('access_point_id') == $point->id ? 'selected' : '' }}>{{ $point->ssid }}</option>
                                                    @endforeach
                                                </select>
                                                @error('access_point_id')
                                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                                @enderror
                                                <svg viewBox="0 0 16 16" fill="currentColor" data-slot="icon" aria-hidden="true" class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4">
                                                    <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="col-span-full">
                                            <label for="street_address" class="block text-sm/6 font-medium text-gray-900">Street address</label>
                                            <div class="mt-2">
                                                <input id="street_address" type="text" name="street_address" value="{{ old('street_address') }}" autocomplete="street_address" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                @error('street_address')
                                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="sm:col-span-2 sm:col-start-1">
                                            <label for="city" class="block text-sm/6 font-medium text-gray-900">City</label>
                                            <div class="mt-2">
                                                <input id="city" type="text" name="city" autocomplete="address-level2" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                            </div>
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label for="region" class="block text-sm/6 font-medium text-gray-900">State / Province</label>
                                            <div class="mt-2">
                                                <input id="region" type="text" name="region" autocomplete="address-level1" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                            </div>
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label for="postal-code" class="block text-sm/6 font-medium text-gray-900">ZIP / Postal code</label>
                                            <div class="mt-2">
                                                <input id="postal-code" type="text" name="postal-code" autocomplete="postal-code" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex items-center justify-end gap-x-6">
                                <a href="{{ route('clients')}}" type="button" class="rounded-md text-sm font-semibold px-3 py-2 text-gray-900 hover:bg-indigo-500 hover:text-white">Cancel</a>
                                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>