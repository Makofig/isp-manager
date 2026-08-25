<div>
    <div class="p-6" wire:poll.30s><!-- wire:poll.30s -->
        <h2 class="text-2xl font-bold mb-6">📊 Dashboard</h2>

        <!-- Métricas rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white p-4 shadow rounded-lg">
                <p class="text-gray-600">Clientes</p>
                <h3 class="text-2xl font-bold">{{ $totalClients }}</h3>
            </div>
            <div class="bg-white p-4 shadow rounded-lg">
                <p class="text-gray-600">Total Cuotas Emitidas</p>
                <h3 class="text-2xl font-bold">{{ $totalPayments }}</h3>
            </div>
            <div class="bg-red-100 p-4 shadow rounded-lg">
                <p class="text-gray-600">Megabytes</p>
                <h3 class="text-2xl font-bold text-red-700">{{ number_format($totalMegabytesUsed ?? 0, 0) }} MB</h3>
            </div>
            <div class="bg-green-100 p-4 shadow rounded-lg">
                <p class="text-gray-600">Pagado</p>
                <h3 class="text-2xl font-bold text-green-700">${{ number_format($totalPaid, 2) }}</h3>
            </div>
            <div class="bg-red-100 p-4 shadow rounded-lg">
                <p class="text-gray-600">Deuda</p>
                <h3 class="text-2xl font-bold text-red-700">${{ number_format($totalDebt, 2) }}</h3>
            </div>
        </div>

        <!-- Profitability Metrics -->
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 p-6 rounded-xl mb-8 border border-indigo-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Profitability (Current Month)</h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <p class="text-xs text-gray-500">Revenue</p>
                    <p class="text-xl font-bold text-green-600">${{ number_format($monthRevenue ?? 0, 2) }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <p class="text-xs text-gray-500">Provider Costs</p>
                    <p class="text-xl font-bold text-red-600">${{ number_format($providerCosts ?? 0, 2) }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <p class="text-xs text-gray-500">Expenses</p>
                    <p class="text-xl font-bold text-red-600">${{ number_format($monthExpenses ?? 0, 2) }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <p class="text-xs text-gray-500">Net Profit</p>
                    <p class="text-xl font-bold {{ ($netProfit ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ${{ number_format($netProfit ?? 0, 2) }}
                    </p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <p class="text-xs text-gray-500">Margin</p>
                    <p class="text-xl font-bold {{ ($marginPercent ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($marginPercent ?? 0, 1) }}%
                    </p>
                </div>
            </div>
        </div>

        <!-- Graficos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Pagos por mes -->
            <div class="bg-white p-4 shadow rounded-lg">
                <h3 class="text-lg font-semibold mb-4">Pagos por Mes</h3>
                <canvas id="paymentsPerMonthChart"></canvas>
            </div>

            <!-- Estados -->
            <div class="bg-white p-4 shadow rounded-lg">
                <h3 class="text-lg font-semibold mb-4">Estados de Pagos</h3>
                <canvas id="paymentStatusesChart"></canvas>
            </div>
        </div>

        <!-- Top deudores -->
        <div class="bg-white p-4 shadow rounded-lg">
            <h3 class="text-lg font-semibold mb-4">🔝 Top 5 Deudores</h3>
            <table class="min-w-full border border-gray-200 table-fixed">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left w-[700px]">Cliente</th>
                        <th class="px-4 py-2 text-left">Deuda Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topDebtors as $debtor)
                    <tr class="border-t">
                        <td class="px-4 py-2 truncate w-[700px]">{{ $debtor['apellido'] . ' ' . $debtor['nombre'] ?? 'N/A' }}</td>
                        <td class="px-4 py-2 text-red-600 font-bold">
                            ${{ number_format($debtor['deuda'] ?? 0, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-white p-4 shadow rounded-lg">
            <h3 class="text-lg font-semibold mb-4">🔝 Top 5 Pagos</h3>
            <table class="min-w-full border border-gray-200 table-fixed">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left w-[700px]">Cliente</th>
                        <th class="px-4 py-2 text-left">Total Pagado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topPayers as $payer)
                    <tr class="border-t">
                        <td class="px-4 py-2 truncate w-[700px]">{{ $payer['apellido'] . ' ' . $payer['nombre'] ?? 'N/A' }}</td>
                        <td class="px-4 py-2 text-green-600 font-bold">
                            ${{ number_format($payer['total_paid'] ?? 0, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // WebSocket: Auto-refresh dashboard on real-time events
        document.addEventListener('DOMContentLoaded', () => {
            window.addEventListener('dashboard-payment-updated', () => {
                @this.call('refreshData');
            });
            window.addEventListener('dashboard-expense-updated', () => {
                @this.call('refreshData');
            });
            window.addEventListener('dashboard-provider-updated', () => {
                @this.call('refreshData');
            });
        });
    </script>
    @endpush
</div>