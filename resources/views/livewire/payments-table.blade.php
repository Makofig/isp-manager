<div>
    {{-- Success is as dangerous as failure. --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            @if($client)
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Amount
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Amount Paid
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Payment Date
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
                 @foreach($payments as $payment)
                 <tr class="hover:bg-gray-50 transition-colors duration-150">
                     <td class="px-6 py-4 whitespace-nowrap">
                         <div class="text-sm font-medium text-gray-900">
                             {{ optional($payment->cuota)->created_at ? $payment->cuota->created_at->format('M d, Y') : 'N/A' }}
                         </div>
                     </td>
                     <td class="px-6 py-4 whitespace-nowrap">
                         <div class="text-sm text-gray-900">$ {{ number_format($payment->costo, 2) }}</div>
                     </td>
                     <td class="px-6 py-4 whitespace-nowrap">
                         <div class="text-sm {{ $payment->abonado > 0 ? 'text-green-600 font-medium' : 'text-gray-900' }}">
                             $ {{ number_format($payment->abonado, 2) }}
                             @if($payment->has_partial_payment)
                                 <span class="text-xs text-yellow-600">(partial)</span>
                             @endif
                         </div>
                     </td>
                     <td class="px-6 py-4 whitespace-nowrap">
                         <div class="px-2 text-xs inline-flex rounded-full {{ $payment->fecha_pago ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                             {{ $payment->fecha_pago ?? 'Pending' }}
                         </div>
                     </td>
                     <td class="px-6 py-4 whitespace-nowrap">
                         @if ($payment->is_paid)
                         <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                             Paid
                         </span>
                         @elseif($payment->has_partial_payment)
                         <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                             Partial
                         </span>
                         @else
                         <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                             Unpaid
                         </span>
                         @endif
                     </td>
                     <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                         <a href="{{ route('payments.show', $payment->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Show</a>
                         @if ($payment->is_paid)
                         <button class="text-gray-600 cursor-not-allowed mr-3" disabled>Paid</button>
                         @else
                         <a href="{{ route('payments.edit', $payment->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                             {{ $payment->has_partial_payment ? 'Add Payment' : 'Pay' }}
                         </a>
                         @endif
                     </td>
                 </tr>
                 @endforeach
            </tbody>
            @else
            <tr>
                <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                    No hay información disponible para este cliente.
                </td>
            </tr>
            @endif
        </table>
    </div>
    <!-- Pagination -->
    <x-pagination :paginator="$payments" />
</div>