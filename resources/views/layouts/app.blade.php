<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'InterSys') }} — Network Management</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Chart.js (for dashboard/statistics pages) -->
        <script>
            window.chartColors = {
                accent: '#58A6FF',
                success: '#3FB950',
                warning: '#D29922',
                danger: '#F85149',
                muted: '#5D6670',
                text: '#8B949E',
                border: '#252D36',
            };
        </script>

        @livewireStyles
    </head>

    <body class="font-sans antialiased">
        <div x-data class="min-h-screen bg-dark-bg">
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.store('sidebar', {
                        collapsed: false,
                        toggle() {
                            this.collapsed = !this.collapsed;
                        }
                    });
                });
            </script>
            <!-- Sidebar -->
            <x-sidebar />

            <!-- Main Content Area -->
            <div :class="$store.sidebar.collapsed ? 'lg:ml-[72px]' : 'lg:ml-[260px]'" x-data class="transition-all duration-150">
                <!-- Topbar -->
                <x-topbar />

                <!-- Page Content -->
                <main class="min-h-[calc(100vh-64px)] p-6 lg:p-8 max-w-8xl mx-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Global UI containers -->
        <x-ui-toast position="top-right" />

        @stack('modals')
        @livewireScripts
    </body>
</html>
