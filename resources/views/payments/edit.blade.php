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
                                <h2 class="text-xl font-bold text-gray-800">Pay - {{ optional($payment->cuota)->created_at ? $payment->cuota->created_at->format('M d, Y') : 'N/A' }}</h2>
                                <p class="text-gray-500 mt-1">Manage information payment here.</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap py-10 px-2">
                        <img
                            class="h-32 w-32 rounded-full object-cover"
                            src="{{ $payment->cliente?->imagen
                                        ? asset('storage/clients/' . $payment->cliente?->imagen)
                                        : asset('images/default-avatar.png') }}"
                            alt="{{ $payment->cliente?->nombre }} {{ $payment->cliente?->apellido }}">
                        <div class="flex flex-col justify-center ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">{{ $payment->cliente?->nombre }} {{ $payment->cliente?->apellido }}</h3>
                            <p class="text-gray-500">Phone: {{ $payment->cliente?->telefono }}</p>
                            <p class="text-gray-500">IP: {{ $payment->cliente?->ip }}</p>
                            <p class="text-gray-500">{{ $payment->cliente?->direccion }}</p>
                        </div>
                    </div>
                    <div>
                        <div>
                            <div class="flex justify-center py-5">
                                <div class="w-full md:w-2/4 bg-white border border-gray-300 rounded-lg shadow-md p-6">
                                    <div class="text-gray-700">
                                        <div class="flex justify-center mb-4">
                                            <div class="text-center">
                                                <p id="price" class="text-lg font-semibold">
                                                    Fee: <strong id="priceValue" data-price="{{ $payment->costo }}" class="ml-1 text-indigo-600">$ {{ number_format($payment->costo, 2) }}</strong>
                                                </p>
                                                @if($payment->abonado > 0)
                                                <p class="text-sm text-green-600 mt-1">
                                                    Paid: $ {{ number_format($payment->abonado, 2) }} |
                                                    Remaining: <strong class="text-red-600">$ {{ number_format($payment->remaining_amount, 2) }}</strong>
                                                </p>
                                                @endif
                                            </div>
                                        </div>

                                        <form id="update-{{ $payment->id }}" action="{{ route('payments.update', $payment->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col items-center">
                                            @csrf
                                            @method('PUT')

                                            <!-- Amount -->
                                            <div class="mb-4 w-full">
                                                <label for="amount" class="block text-gray-700 font-bold mb-2">Amount:</label>
                                                <input type="number" name="amount" id="amount"
                                                    value="{{ old('amount', $payment->abonado) }}"
                                                    step="0.01"
                                                    class="shadow border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-indigo-300"
                                                    required>
                                                <p id="amountError" class="mt-2 text-sm hidden"></p>
                                                @error('amount')
                                                <p class="text-red-500 text-xs italic mt-2 text-center">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Coment -->
                                            <div class="mb-4 w-full">
                                                <label for="coment" class="block text-gray-700 font-bold mb-2">Coment:</label>
                                                <input type="text" name="coment" id="coment"
                                                    value="{{ old('coment', $payment->comentario) }}"
                                                    class="shadow border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-indigo-300"
                                                    required>
                                                @error('coment')
                                                <p class="text-red-500 text-xs italic mt-2 text-center">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Payment Date -->
                                            <div class="mb-4 w-full">
                                                <label for="payment_date" class="block text-gray-700 font-bold mb-2">Payment Date:</label>
                                                <input type="date" name="payment_date" id="payment_date"
                                                    value="{{ old('payment_date', $payment->fecha_pago) }}"
                                                    class="shadow border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-indigo-300"
                                                    required>
                                                @error('payment_date')
                                                <p class="text-red-500 text-xs italic mt-2 text-center">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Voucher -->
                                            <div class="mb-4 w-full">
                                                <p class="flex justify-center mb-2 font-bold">Upload payment voucher (optional)</p>
                                                <input type="file" name="voucher" id="voucher"
                                                    class="shadow border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-indigo-300">
                                                @error('voucher')
                                                <p class="text-red-500 text-xs italic mt-2 text-center">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Botón -->
                                            <div class="mt-4">
                                                <button type="button"
                                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                                    onclick="confirmUpdate(`{{ $payment->id }}`)">
                                                    Save Payment
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <x-button-previous />
                    </div>
                </div>
            </div>     
        </div>
    </div>
</x-app-layout>
<script>
    function confirmUpdate(paymentId) {
        Swal.fire({
            title: 'You\'re sure?',
            text: "This action will update the payment details.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`update-${paymentId}`).submit();
            }
        })
    }

    // Validación del campo de monto (permite pagos parciales)
    document.addEventListener("DOMContentLoaded", () => {
        const amountInput = document.getElementById("amount");
        const priceValue = parseFloat(document.getElementById("priceValue").dataset.price);
        const errorMessage = document.getElementById("amountError");

        amountInput.addEventListener("input", () => {
            const value = parseFloat(amountInput.value);

            if (isNaN(value) || value <= 0) {
                amountInput.classList.remove("border-green-500", "border-yellow-500", "border-red-500");
                errorMessage.classList.add("hidden");
                return;
            }

            if (value < priceValue) {
                // Pago parcial - permitido pero con advertencia
                amountInput.classList.remove("border-green-500", "border-red-500");
                amountInput.classList.add("border-yellow-500");
                errorMessage.textContent = `Partial payment. Remaining after: $${(priceValue - value).toFixed(2)}`;
                errorMessage.classList.remove("hidden", "text-red-600", "text-green-600");
                errorMessage.classList.add("text-yellow-600");
            } else if (value === priceValue) {
                // Pago exacto
                amountInput.classList.remove("border-yellow-500", "border-red-500");
                amountInput.classList.add("border-green-500");
                errorMessage.textContent = "Exact payment amount.";
                errorMessage.classList.remove("hidden", "text-red-600", "text-yellow-600");
                errorMessage.classList.add("text-green-600");
            } else {
                // Monto excedido
                amountInput.classList.remove("border-green-500", "border-yellow-500");
                amountInput.classList.add("border-red-500");
                errorMessage.textContent = `Amount exceeds fee value ($${priceValue.toFixed(2)}).`;
                errorMessage.classList.remove("hidden", "text-green-600", "text-yellow-600");
                errorMessage.classList.add("text-red-600");
            }
        });
    });
</script>