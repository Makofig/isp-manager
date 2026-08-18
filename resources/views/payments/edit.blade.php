<x-app-layout>
<x-page-header title="Update Payment" description="Client: {{ $payment->clients->nombre ?? 'N/A' }} {{ $payment->clients->apellido ?? '' }}" />

    <div class="max-w-lg">
        <div class="stat-card mb-4">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium text-muted uppercase tracking-wider">Price</span>
                <span class="text-lg font-semibold text-text-primary">${{ number_format($payment->costo, 2) }}</span>
            </div>
        </div>

        <form id="update-{{ $payment->id }}" action="{{ route('payments.update', $payment->id) }}" method="POST" enctype="multipart/form-data" class="stat-card space-y-4">
            @csrf
            @method('PUT')

            <x-ui-input type="number" name="amount" label="Amount" placeholder="0.00" :value="old('amount', $payment->abonado)" step="0.01" :error="$errors->first('amount')" />
            <p id="amountError" class="text-xs hidden -mt-2"></p>

            <x-ui-input type="text" name="coment" label="Comment" placeholder="Payment note..." :value="old('coment', $payment->comentario)" :error="$errors->first('coment')" />

            <x-ui-input type="date" name="payment_date" label="Payment Date" :value="old('payment_date', $payment->fecha_pago)" :error="$errors->first('payment_date')" />

            <div class="space-y-1.5">
                <label class="block text-[11px] font-medium text-muted uppercase tracking-wider">Voucher</label>
                <input type="file" name="voucher" id="voucher" class="input-dark w-full text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-[6px] file:border-0 file:text-xs file:font-medium file:bg-accent/10 file:text-accent hover:file:bg-accent/20" />
                @error('voucher'))
                    <p class="text-xs text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ url()->previous() }}" class="btn-ghost text-sm">Cancel</a>
                <button type="submit" class="btn-primary text-sm">Save Payment</button>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const amountInput = document.getElementById("amount");
        const priceValue = parseFloat(document.getElementById("priceValue")?.dataset.price || 0);
        const errorMessage = document.getElementById("amountError");

        if (amountInput && errorMessage) {
            amountInput.addEventListener("input", () => {
                const value = parseFloat(amountInput.value);

                if (isNaN(value)) {
                    amountInput.classList.remove("border-green-500", "border-red-500");
                    errorMessage.classList.add("hidden");
                    return;
                }

                if (value < priceValue) {
                    amountInput.classList.remove("border-green-500");
                    amountInput.classList.add("border-danger");
                    errorMessage.textContent = `The amount entered is less than the price of the installment ($${priceValue.toFixed(2)}).`;
                    errorMessage.classList.remove("hidden");
                    errorMessage.classList.add("text-danger");
                    errorMessage.classList.remove("text-success");
                } else if (value === priceValue) {
                    amountInput.classList.remove("border-danger");
                    amountInput.classList.add("border-success");
                    errorMessage.textContent = "Correct amount.";
                    errorMessage.classList.remove("hidden");
                    errorMessage.classList.remove("text-danger");
                    errorMessage.classList.add("text-success");
                } else {
                    amountInput.classList.remove("border-success");
                    amountInput.classList.add("border-danger");
                    errorMessage.textContent = `The amount cannot exceed the value of the installment ($${priceValue.toFixed(2)}).`;
                    errorMessage.classList.remove("hidden");
                    errorMessage.classList.add("text-danger");
                    errorMessage.classList.remove("text-success");
                }
            });
        }
    });
</script>
