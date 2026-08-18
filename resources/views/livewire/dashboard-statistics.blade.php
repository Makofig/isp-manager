<div wire:poll.30s>
    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            title="TOTAL CLIENTS"
            :value="$totalClients"
            subtitle="active"
            icon="users"
        />
        <x-stat-card
            title="BANDWIDTH SOLD"
            :value="number_format($totalMegabytesUsed, 0) . ' MB'"
            subtitle="total capacity"
            icon="wifi"
        />
        <x-stat-card
            title="REVENUE"
            :value="'$' . number_format($totalPaid, 2)"
            subtitle="collected"
            icon="bar-chart-2"
        />
        <x-stat-card
            title="DEBTORS"
            :value="'$' . number_format($totalDebt, 2)"
            subtitle="outstanding"
            icon="alert-triangle"
        />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="stat-card">
            <h3 class="text-[11px] font-medium text-muted uppercase tracking-wider mb-4">Payments per Month</h3>
            <div class="relative h-64">
                <canvas id="paymentsPerMonthChart"></canvas>
            </div>
        </div>
        <div class="stat-card">
            <h3 class="text-[11px] font-medium text-muted uppercase tracking-wider mb-4">Payment Status</h3>
            <div class="relative h-64">
                <canvas id="paymentStatusesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="stat-card">
            <h3 class="text-[11px] font-medium text-muted uppercase tracking-wider mb-4">Top Debtors</h3>
            @if(count($topDebtors) > 0)
                <div class="space-y-2">
                    @foreach ($topDebtors as $debtor)
                        <div class="flex items-center justify-between py-2 border-b border-dark-border last:border-0">
                            <span class="text-sm text-text-primary truncate">{{ ($debtor['apellido'] ?? '') . ' ' . ($debtor['nombre'] ?? 'N/A') }}</span>
                            <span class="text-sm font-medium text-danger">${{ number_format($debtor['deuda'] ?? 0, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-muted py-4">No debtors found.</p>
            @endif
        </div>

        <div class="stat-card">
            <h3 class="text-[11px] font-medium text-muted uppercase tracking-wider mb-4">Top Payers</h3>
            @if(count($topPayers) > 0)
                <div class="space-y-2">
                    @foreach ($topPayers as $payer)
                        <div class="flex items-center justify-between py-2 border-b border-dark-border last:border-0">
                            <span class="text-sm text-text-primary truncate">{{ ($payer['apellido'] ?? '') . ' ' . ($payer['nombre'] ?? 'N/A') }}</span>
                            <span class="text-sm font-medium text-success">${{ number_format($payer['total_paid'] ?? 0, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-muted py-4">No payments yet.</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const colors = window.chartColors || {
            accent: '#58A6FF', success: '#3FB950', warning: '#D29922',
            danger: '#F85149', muted: '#5D6670', text: '#8B949E', border: '#252D36',
        };

        const paymentsData = @json($paymentsPerMonth);
        if (Object.keys(paymentsData).length > 0) {
            const months = Object.keys(paymentsData);
            new Chart(document.getElementById('paymentsPerMonthChart'), {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        { label: 'Cuotas Emitidas', data: months.map(m => paymentsData[m].totalCuotas || 0), backgroundColor: colors.accent },
                        { label: 'Pagado', data: months.map(m => paymentsData[m].totalPagado || 0), backgroundColor: colors.success },
                        { label: 'Deuda', data: months.map(m => paymentsData[m].deuda || 0), backgroundColor: colors.danger },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { color: colors.text, font: { family: 'JetBrains Mono' } } } },
                    scales: {
                        x: { ticks: { color: colors.text }, grid: { color: colors.border } },
                        y: { ticks: { color: colors.text }, grid: { color: colors.border } },
                    }
                }
            });
        }

        const statusesData = @json($paymentStatuses);
        if (Object.keys(statusesData).length > 0) {
            const labels = Object.keys(statusesData).map(s => s == 1 ? 'Pagado' : 'Pendiente');
            new Chart(document.getElementById('paymentStatusesChart'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: Object.values(statusesData),
                        backgroundColor: [colors.success, colors.danger],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { color: colors.text, font: { family: 'JetBrains Mono' } } } },
                }
            });
        }
    });
</script>
@endpush
