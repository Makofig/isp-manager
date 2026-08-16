<div>
    {{-- The best athlete wants his opponent at his best. --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quota') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container max-w-6xl">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="max-w-2xl mx-auto py-10">
                        <form wire:submit.prevent="save">
                            <div class="space-y-12">
                                <div class="border-b border-gray-900/10 pb-12">
                                    <h2 class="text-base/7 font-semibold text-gray-900">Emitir Quota</h2>
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
                                    <h2 class="text-base/7 font-semibold text-gray-900">Information the Quota</h2>

                                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                        <div class="sm:col-span-3">
                                            <label for="months_year" class="block text-sm/6 font-medium text-gray-900">Months</label>
                                            <div class="mt-2">
                                                <input id="months_year" type="text" wire:model="months_year" value=""
                                                    autocomplete="off"
                                                    class="block w-full rounded-md bg-gray-100 px-3 py-1.5 text-base text-gray-700 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 cursor-not-allowed" />
                                                @error('months_year')
                                                <span class="text-red-600 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex items-center justify-end gap-x-6">
                                <a href="{{ route('quota') }}" type="button" class="rounded-md text-sm font-semibold px-3 py-2 text-gray-900 hover:bg-indigo-500 hover:text-white">Cancel</a>
                                <button type="button"
                                    wire:click="confirmCreate"
                                    wire:loading.attr="disabled"
                                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Save</button>
                            </div>
                        </form>
                        @if($loading)
                        <div wire:poll.300ms="checkProgress"
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">

                            <div class="bg-white p-6 rounded-2xl w-96 text-center">

                                @if($status === 'processing')
                                <h2 class="text-lg font-semibold mb-4">
                                    Generating Fees...
                                </h2>

                                <div class="w-full bg-gray-200 rounded-full h-4">
                                    <div class="bg-indigo-600 h-4 rounded-full transition-all duration-300"
                                        style="width: {{ $progress }}%">
                                    </div>
                                </div>

                                <p class="mt-2 text-sm text-gray-600">
                                    {{ $progress }}%
                                </p>
                                @endif

                                @if($status === 'success')
                                <div class="text-green-600 text-4xl mb-2">✔</div>
                                <h2 class="text-lg font-semibold">
                                    Quota generated successfully
                                </h2>
                                @endif

                            </div>
                        </div>
                        @endif
                        <!-- @if($loading)
                            <div 
                                wire:poll.1000ms="checkProgress"
                                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
                            >
                                <div class="bg-white p-6 rounded-2xl w-96">
                                    <h2 class="text-lg font-semibold mb-4">
                                        Generating Fees...
                                    </h2>

                                    <div class="w-full bg-gray-200 rounded-full h-4">
                                        <div class="bg-blue-600 h-4 rounded-full transition-all"
                                            style="width: {{ $progress }}%">
                                        </div>
                                    </div>

                                    <p class="mt-2 text-sm text-gray-600">
                                        {{ $progress }}%
                                    </p>
                                </div>
                            </div>
                        @endif -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // function confirmCreate() {
        //     Swal.fire({
        //         title: 'You\'re sure?',
        //         text: "This action will create the quota for the current month.",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         cancelButtonColor: '#3085d6',
        //         confirmButtonText: 'Yes, create it!',
        //         cancelButtonText: 'Cancel'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             document.getElementById('create-form').submit();
        //         }
        //     })
        // }
        Livewire.on('show-confirm', () => {
            Swal.fire({
                title: "You're sure?",
                text: "This action will create the quota for the current month.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, create it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('execute-save');
                }
            })
        })

        Livewire.on('quota-finished', () => {
            setTimeout(() => {
                @this.loading = false
                @this.status = 'processing'
            }, 1000)
        })

        Livewire.on('execute-save', () => {
            @this.save()
        })
    </script>
</div>