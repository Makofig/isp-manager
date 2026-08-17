<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- AlpineJS -->
        <!-- <script src="//unpkg.com/alpinejs" defer></script> -->
        <!-- Styles -->
        <script>
            window.paymentsChart = window.paymentsChart || null;
            window.statusesChart = window.statusesChart || null;

            function renderCharts(paymentsPerMonth, paymentStatuses) {

                if (!paymentsPerMonth || !paymentStatuses) {
                    console.log(paymentStatuses, paymentsPerMonth);
                    console.warn('Datos de gráficos inválidos');
                    return;
                }

                const months = Object.keys(paymentsPerMonth);
                const cuotas = months.map(m => paymentsPerMonth[m].totalCuotas);
                const pagados = months.map(m => paymentsPerMonth[m].totalPagado);
                const deudas = months.map(m => paymentsPerMonth[m].deuda);

                if (window.paymentsChart) window.paymentsChart.destroy();

                window.paymentsChart = new Chart(
                    document.getElementById('paymentsPerMonthChart'),
                    {
                        type: 'bar',
                        data: {
                            labels: months,
                            datasets: [
                                { label: 'Cuotas Emitidas', data: cuotas, backgroundColor: '#3b82f6' },
                                { label: 'Pagado', data: pagados, backgroundColor: '#10b981' },
                                { label: 'Deuda', data: deudas, backgroundColor: '#ef4444' },
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    }
                );

                const statusLabels = Object.keys(paymentStatuses).map(s => s == 1 ? 'Pagado' : 'Pendiente');
                const statusData = Object.values(paymentStatuses);

                const backgroundColors = Object.keys(paymentStatuses).map(s =>
                    s == 1 ? '#10b981' : '#ef4444'
                );

                if (window.statusesChart) window.statusesChart.destroy();

                window.statusesChart = new Chart(
                    document.getElementById('paymentStatusesChart'),
                    {
                        type: 'pie',
                        data: {
                            labels: statusLabels,
                            datasets: [{
                                data: statusData,
                                backgroundColor: backgroundColors
                            }]
                        },
                        options: { responsive: true }
                    }
                );
            }

            document.addEventListener('livewire:init', () => {

                Livewire.on('refreshCharts', (event) => { 

                    const payload = event[0]; // 👈 ESTE ES EL DETALLE

                    renderCharts(
                        payload.paymentsPerMonth,
                        payload.paymentStatuses
                    );

                });

            });
        </script> 
        @livewireStyles
    </head>
   
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')
        @stack('scripts')

        @livewireScripts
        @stack('scripts')
    </body>
</html>
