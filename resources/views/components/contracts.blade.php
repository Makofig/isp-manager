<div class="container max-w-6xl">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Table Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Contracts</h2>
                    <p class="text-gray-500 mt-1">Manage your contracts and their account permissions here.</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('contracts.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                        Add Contract
                    </a>
                </div>
            </div>
            @if(session('success'))
            <script>
            Swal.fire({
                icon: 'success',
                title: '¡Hecho!',
                text: `{{ session('success') }}`,
                timer: 2000,
                showConfirmButton: false
            })
            </script>
            @endif
            <!-- Search and Filter -->
            <div class="mt-6 flex flex-col sm:flex-row gap-4">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full " placeholder="Search contracts...">
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Megabytes
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Price
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Client Count
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Row 1 -->
                    @foreach ($contracts as $contract)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $contract->nombre }}</div>
                                    <div class="text-sm text-gray-500">{{ $contract->nombre }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $contract->megabytes }} mb</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">$ {{ number_format($contract->costo, 2, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="px-2 text-xs inline-flex rounded-full bg-green-100 text-green-800">{{ $contract->cliente->count() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('contracts.edit', $contract->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form id="delete-contract-{{ $contract->id }}"
                                action="{{ route('contracts.destroy', $contract->id) }}"
                                method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>

                            <button type="button"
                                    class="text-red-600 hover:text-red-900"
                                    onclick="confirmDelete(`{{ $contract->id }}`)">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <x-pagination :paginator="$contracts" />
    </div>
</div>
<script>
function confirmDelete(contractId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-contract-${contractId}`).submit();
        }
    })
}
</script>
