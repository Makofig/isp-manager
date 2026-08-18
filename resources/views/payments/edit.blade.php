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

            <!-- Custom Date Picker -->
            <div x-data="{
                open: false,
                selectedDate: '{{ old('payment_date', $payment->fecha_pago) }}',
                displayValue: '',
                init() {
                    if (this.selectedDate) {
                        const d = new Date(this.selectedDate + 'T00:00:00');
                        this.displayValue = d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
                    }
                },
                selectDate() {
                    if (this.selectedDate) {
                        const d = new Date(this.selectedDate + 'T00:00:00');
                        this.displayValue = d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
                    }
                    this.open = false;
                }
            }" class="relative">
                <label class="block text-[11px] font-medium text-muted uppercase tracking-wider mb-1.5">Payment Date</label>

                <!-- Input trigger -->
                <div @click="open = !open"
                     class="input-dark w-full text-sm flex items-center justify-between gap-3 cursor-pointer">
                    <span x-text="displayValue || 'Select date...'"
                          :class="displayValue ? 'text-text-primary' : 'text-muted'"
                          class="flex-1"></span>
                    <svg class="w-4 h-4 text-muted flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>

                <!-- Native date input (hidden but functional) -->
                <input type="date" name="payment_date" x-model="selectedDate" @change="selectDate()" class="sr-only" tabindex="-1" />

                <!-- Dropdown -->
                <div x-show="open"
                     @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute z-50 mt-2 w-full bg-dark-surface border border-dark-border rounded-[8px] shadow-lg p-4"
                     style="display: none;">

                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-text-primary">Select Date</span>
                        <button @click="open = false" class="p-1 rounded hover:bg-dark-elevated text-muted hover:text-text-primary">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <!-- Quick date selectors -->
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <button type="button" @click="selectedDate = '{{ date('Y-m-d') }}'; selectDate()"
                                class="py-2 px-3 text-xs font-medium rounded-[6px] bg-dark-elevated text-muted-light hover:bg-accent/10 hover:text-accent transition">
                            Today
                        </button>
                        <button type="button" @click="selectedDate = '{{ date('Y-m-d', strtotime('-1 day')) }}'; selectDate()"
                                class="py-2 px-3 text-xs font-medium rounded-[6px] bg-dark-elevated text-muted-light hover:bg-accent/10 hover:text-accent transition">
                            Yesterday
                        </button>
                        <button type="button" @click="selectedDate = '{{ date('Y-m-d', strtotime('+1 day')) }}'; selectDate()"
                                class="py-2 px-3 text-xs font-medium rounded-[6px] bg-dark-elevated text-muted-light hover:bg-accent/10 hover:text-accent transition">
                            Tomorrow
                        </button>
                    </div>

                    <!-- Native date picker (styled) -->
                    <input type="date"
                           x-model="selectedDate"
                           @change="selectDate()"
                           class="input-dark w-full text-sm" />

                    @error('payment_date'))
                        <p class="text-xs text-danger mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Custom File Input -->
            <div x-data="{
                fileName: '',
                handleFile(event) {
                    const file = event.target.files[0];
                    this.fileName = file ? file.name : '';
                }
            }" class="space-y-2">
                <label class="block text-[11px] font-medium text-muted uppercase tracking-wider">Voucher</label>

                <label class="input-dark w-full flex items-center gap-3 cursor-pointer hover:border-accent/50 transition py-1.5">
                    <span class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-[6px] bg-accent/10 text-accent">
                        Choose file
                    </span>
                    <span class="flex-1 text-sm truncate" x-text="fileName || 'No file selected...'"
                          :class="fileName ? 'text-text-secondary' : 'text-muted'"></span>
                    <svg class="w-4 h-4 text-muted flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <input type="file" name="voucher" id="voucher" class="sr-only" @change="handleFile($event)" />
                </label>

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
