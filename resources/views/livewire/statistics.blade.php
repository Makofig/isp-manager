<div>
    <div>
        <div class="p-6 space-y-6">

            <!-- KPIs -->
            <div class="grid grid-cols-6 gap-6">
                <div class="bg-white p-4 shadow rounded-lg text-center">
                    <h4 class="text-sm text-gray-500">Pagados</h4>
                    <p class="text-2xl font-bold text-blue-600">{{ $clientesConPago }}</p>
                </div>
                <div class="bg-white p-4 shadow rounded-lg text-center">
                    <h4 class="text-sm text-gray-500">Deudores</h4>
                    <p class="text-2xl text-red-600 font-bold">{{ $deudores }}</p>
                </div>
                <div class="bg-white p-4 shadow rounded-lg text-center">
                    <h4 class="text-sm text-gray-500">Baneados</h4>
                    <p class="text-2xl text-red-600 font-bold">{{ $clientesBaneados?? 0 }}</p>
                </div>
                <div class="bg-white p-4 shadow rounded-lg text-center">
                    <h4 class="text-sm text-gray-500">Recaudado</h4>
                    <p class="text-2xl font-bold text-green-600">${{ number_format($recaudado,0) }}</p>
                </div>
                <div class="bg-white p-4 shadow rounded-lg text-center">
                    <h4 class="text-sm text-gray-500">Pendiente</h4>
                    <p class="text-2xl font-bold text-red-600">${{ number_format($pendiente,0) }}</p>
                </div>
                <div class="bg-white p-4 shadow rounded-lg text-center">
                    <h4 class="text-sm text-gray-500">Morosidad</h4>
                    <p class="text-2xl font-bold text-orange-500">{{ $morosidad }}%</p>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white p-4 shadow rounded-lg flex gap-4 items-center">
                <div>
                    <label class="block text-sm font-medium">Mes</label>
                    <select wire:model.live="mes" class="border rounded p-2 w-32">
                        @foreach(range(1,12) as $m)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Año</label>
                    <select wire:model.live="anio" class="border rounded p-2 w-20">
                        @foreach(range(2023, now()->year) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Gráficas -->
            <div class="grid grid-cols-2 gap-6">
                <div class="bg-white p-4 shadow rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">📊 Puntualidad de Pagos</h3>
                    <canvas id="chartPuntualidad"></canvas>
                </div>
                <!-- <div class="bg-white p-4 shadow rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">💳 Deudores</h3>
                    <p class="text-2xl text-red-600 font-bold">{{ $deudores }}</p>
                </div> -->
            </div>

            <!-- Recaudación + Morosos -->
            <div class="grid grid-cols-2 gap-6">
                <div wire:ignore class="bg-white p-4 shadow rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">💰 Recaudación Mensual</h3>
                    <canvas id="chartRecaudacion"></canvas>
                </div>
                <div class="bg-white p-4 shadow rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">🚨 Clientes Morosos (mes actual)</h3>
                    <!-- Tabla con altura fija -->
                    <div class="max-h-[500px] overflow-y-auto">
                        <table class="min-w-full border">
                            <thead class="bg-gray-100 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 text-left">Cliente</th>
                                    <th class="px-4 py-2 text-left">Monto Pendiente</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($morosos as $c)
                                <tr class="border-t">
                                    <td class="px-4 py-2">{{ $c->nombre }}</td>
                                    <td class="px-4 py-2 text-red-600">
                                        ${{ number_format($c->contract->costo,0) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Función para actualizar los gráficos
        function updateCharts(recaudado, pendiente, rangos) {
            chartPuntualidad.config.data.datasets[0].data = [rangos['1-10'], rangos['11-20'], rangos['>21']];
            chartPuntualidad.update();

            chartRecaudacion.config.data.datasets[0].data = [recaudado, pendiente];
            chartRecaudacion.update();
        }

        const chartPuntualidad = new Chart(document.getElementById('chartPuntualidad'), {
            type: 'bar',
            data: {
                labels: ['1-10 días', '11-20 días', '>21 días'],
                datasets: [{
                    label: 'Clientes',
                    data: [{{ $rangos['1-10'] }}, {{ $rangos['11-20'] }}, {{ $rangos['>21'] }}],
                    backgroundColor: ['#4CAF50', '#FFC107', '#F44336']
                }]
            }
        });

        const chartRecaudacion = new Chart(document.getElementById('chartRecaudacion'), {
            type: 'doughnut',
            data: {
                labels: ['Recaudado', 'Pendiente'],
                datasets: [{
                    data: [{{ $recaudado }}, {{ $pendiente }}],
                    backgroundColor: ['#4CAF50', '#F44336']
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

    document.addEventListener("livewire:init", () => {
        Livewire.on('updateCharts', (data) => {
            console.log("📊 Datos recibidos:", data);

            const { recaudado, pendiente, rangos } = data[0];
            
            updateCharts(recaudado, pendiente, rangos);
        });
    });
</script>
    @endpush
</div>