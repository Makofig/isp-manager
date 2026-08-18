<div>
    <!-- KPIs -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <x-stat-card title="PAID" :value="$clientesConPago" icon="check" />
        <x-stat-card title="DEBTORS" :value="$deudores" icon="alert-triangle" />
        <x-stat-card title="BANNED" :value="$clientesBaneados" icon="ban" />
        <x-stat-card title="REVENUE" :value="'$' . number_format($recaudado, 0)" icon="bar-chart-2" />
        <x-stat-card title="PENDING" :value="'$' . number_format($pendiente, 0)" icon="alert-triangle" />
        <x-stat-card title="DELINQUENCY" :value="$morosidad . '%'" icon="alert-triangle" />
    </div>

    <!-- Filters -->
    <div class="stat-card flex flex-wrap gap-3 items-center mb-6">
        <div>
            <label class="block text-[11px] font-medium text-muted uppercase tracking-wider mb-1">Month</label>
            <select wire:model.live="mes" class="input-dark w-32 text-sm">
                @foreach(range(1,12) as $m)
                <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-medium text-muted uppercase tracking-wider mb-1">Year</label>
            <select wire:model.live="anio" class="input-dark w-24 text-sm">
                @foreach(range(2023, now()->year) as $y)
                <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="stat-card">
            <h3 class="text-[11px] font-medium text-muted uppercase tracking-wider mb-4">Payment Punctuality</h3>
            <div class="relative h-64">
                <canvas id="chartPuntualidad"></canvas>
            </div>
        </div>
        <div class="stat-card" wire:ignore>
            <h3 class="text-[11px] font-medium text-muted uppercase tracking-wider mb-4">Monthly Revenue</h3>
            <div class="relative h-64">
                <canvas id="chartRecaudacion"></canvas>
            </div>
        </div>
    </div>

    <!-- Morosos Table -->
    <div class="stat-card">
        <h3 class="text-[11px] font-medium text-muted uppercase tracking-wider mb-4">Delinquent Clients (Current Month)</h3>
        @if(count($morosos) > 0)
            <div class="max-h-[400px] overflow-y-auto">
                <table class="min-w-full">
                    <thead class="sticky top-0 bg-dark-surface">
                        <tr>
                            <th class="px-4 py-2 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Client</th>
                            <th class="px-4 py-2 text-left text-[11px] font-medium text-muted uppercase tracking-wider">Pending Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-border">
                        @foreach($morosos as $c)
                        <tr>
                            <td class="px-4 py-2.5 text-sm text-text-primary">{{ $c->nombre }}</td>
                            <td class="px-4 py-2.5 text-sm text-danger font-medium">${{ number_format($c->contract->costo ?? 0, 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-muted py-4">No delinquent clients this month.</p>
        @endif
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function updateCharts(recaudado, pendiente, rangos) {
            if (chartPuntualidad) {
                chartPuntualidad.config.data.datasets[0].data = [rangos['1-10'], rangos['11-20'], rangos['>21']];
                chartPuntualidad.update();
            }
            if (chartRecaudacion) {
                chartRecaudacion.config.data.datasets[0].data = [recaudado, pendiente];
                chartRecaudacion.update();
            }
        }

        const colors = window.chartColors || { accent: '#58A6FF', success: '#3FB950', warning: '#D29922', danger: '#F85149', muted: '#5D6670', text: '#8B949E', border: '#252D36' };

        const chartPuntualidad = new Chart(document.getElementById('chartPuntualidad'), {
            type: 'bar',
            data: {
                labels: ['1-10 days', '11-20 days', '>21 days'],
                datasets: [{
                    label: 'Clients',
                    data: [{{ $rangos['1-10'] ?? 0 }}, {{ $rangos['11-20'] ?? 0 }}, {{ $rangos['>21'] ?? 0 }}],
                    backgroundColor: [colors.success, colors.warning, colors.danger],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: colors.text }, grid: { color: colors.border } },
                    y: { ticks: { color: colors.text }, grid: { color: colors.border } },
                }
            }
        });

        const chartRecaudacion = new Chart(document.getElementById('chartRecaudacion'), {
            type: 'doughnut',
            data: {
                labels: ['Collected', 'Pending'],
                datasets: [{
                    data: [{{ $recaudado ?? 0 }}, {{ $pendiente ?? 0 }}],
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

        document.addEventListener("livewire:init", () => {
            Livewire.on('updateCharts', (data) => {
                const { recaudado, pendiente, rangos } = data[0];
                updateCharts(recaudado, pendiente, rangos);
            });
        });
    </script>
@endpush
