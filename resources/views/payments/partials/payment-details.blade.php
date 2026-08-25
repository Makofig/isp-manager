<div class="flex justify-center py-5">
    <div class="w-full md:w-2/4 bg-white border border-gray-300 rounded-lg shadow-md p-6"
        x-data="{ open: false }"
        @keydown.escape.window="open = false">

        @if ($payment->is_paid)
        <div class="flex flex-col items-center justify-center mb-4">
            <img src="{{ asset('images/icon-check.svg') }}" alt="Pago realizado" class="w-16 h-16 object-contain">
            <p class="text-green-600 font-semibold py-5">Paid: $ {{ number_format($payment->costo, 2) }}</p>
        </div>
        @elseif ($payment->has_partial_payment)
        <div class="flex flex-col items-center justify-center mb-4">
            <img src="{{ asset('images/icon-partial.svg') }}" alt="Pago parcial" class="w-16 h-16 object-contain">
            <p class="text-yellow-600 font-semibold py-5">Partial Payment</p>
        </div>
        @else
        <div class="flex flex-col items-center justify-center mb-4">
            <img src="{{ asset('images/icon-cancel.svg') }}" alt="Pago pendiente" class="w-16 h-16 object-contain">
            <p class="text-red-600 font-semibold py-5">Pending: $ {{ number_format($payment->costo, 2) }}</p>
        </div>
        @endif

        <h4 class="text-lg font-semibold text-gray-800">Payment Details</h4>
        <p class="text-gray-500">Fee: $ {{ number_format($payment->costo, 2) }}</p>
        @if ($payment->abonado > 0)
        <p class="text-gray-500">Paid: $ {{ number_format($payment->abonado, 2) }}</p>
        <p class="text-gray-500">Remaining: $ {{ number_format($payment->remaining_amount, 2) }}</p>
        @else
        <p class="text-gray-500">Paid: $ 0.00</p>
        @endif
        @if ($payment->comentario)
        <p class="text-gray-500">Comment: {{ $payment->comentario }}</p>
        @endif

        <!-- Comprobante -->
        <div class="mt-4">
            <h4 class="text-lg font-semibold text-gray-800">Payment Voucher</h4>
            @if ($payment->image)
            <img src="{{ asset('storage/' . $payment->image) }}"
                alt="Payment Voucher"
                class="w-32 h-32 object-cover mb-2 cursor-pointer rounded shadow hover:scale-105 transition"
                @click="open = true">

            <a href="{{ asset('storage/' . $payment->image) }}" class="text-blue-500">Download Voucher</a>

            <!-- Modal con fondo blur y diseño responsivo -->
            <div x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90"
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm z-50 p-4"
                @click.self="open = false">

                <!-- Contenedor -->
                <div class="relative bg-white p-3 sm:p-6 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">

                    <!-- Botón de cerrar -->
                    <button @click="open = false"
                        class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl">&times;</button>

                    <!-- Imagen del comprobante -->
                    <div class="flex justify-center items-center flex-1 overflow-auto">
                        <img src="{{ asset('storage/' . $payment->image) }}"
                            alt="Voucher Fullscreen"
                            class="w-full h-auto max-h-[75vh] object-contain rounded-lg">
                    </div>

                    <!-- Botón para descargar -->
                    <div class="mt-4 flex justify-center">
                        <a href="{{ asset('storage/' . $payment->image) }}"
                            download
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition">
                            Descargar Comprobante
                        </a>
                    </div>
                </div>
            </div>
            @else
            <p class="text-gray-500">No voucher available</p>
            @endif
        </div>

        <p class="text-gray-500 mt-4">Created At: {{ $payment->created_at->format('M d, Y') }}</p>
        @if ($payment->fecha_pago == null)
        <p class="text-gray-500">Not date payment</p>
        @else
        <p class="text-gray-500">Date Payment: {{ $payment->fecha_pago }}</p>
        @endif
        @if ($payment->updated_at == null)
        <p class="text-gray-500">Not updated yet</p>
        @else
        <p class="text-gray-500">Updated At: {{ $payment->updated_at->format('M d, Y') }}</p>
        @endif
    </div>
</div>
<x-button-previous />