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

        <!-- Global Confirm Dialog -->
        <div
            id="global-confirm-dialog"
            x-data="{
                open: false,
                title: '¿Estás seguro?',
                message: 'Esta acción no se puede deshacer.',
                action: '',
                formId: '',
                loading: false,
                confirm() {
                    this.loading = true;
                    if (window._confirmCallback) {
                        const cb = window._confirmCallback;
                        window._confirmCallback = null;
                        this.open = false;
                        this.loading = false;
                        cb();
                        return;
                    }
                    if (this.formId) {
                        const form = document.getElementById(this.formId);
                        if (form) form.submit();
                    } else if (this.action) {
                        const f = document.createElement('form');
                        f.method = 'POST';
                        f.action = this.action;
                        f.style.display = 'none';
                        const csrf = document.querySelector('meta[name=csrf-token]');
                        if (csrf) {
                            const t = document.createElement('input');
                            t.type = 'hidden'; t.name = '_token'; t.value = csrf.content;
                            f.appendChild(t);
                        }
                        const m = document.createElement('input');
                        m.type = 'hidden'; m.name = '_method'; m.value = 'DELETE';
                        f.appendChild(m);
                        document.body.appendChild(f);
                        f.submit();
                    }
                    this.open = false;
                    this.loading = false;
                }
            }"
            @keydown.escape.window="open = false"
            class="inline-flex"
            style="display: none;"
        >
            <template x-teleport="body">
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                    style="display: none;"
                >
                    <div
                        @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="bg-dark-surface border border-dark-border rounded-[10px] shadow-2xl max-w-md w-full p-6"
                    >
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center bg-danger/10">
                                <svg class="w-6 h-6 text-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-text-primary" x-text="title"></h3>
                                <p class="mt-1 text-sm text-muted-light" x-text="message"></p>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button @click="open = false" type="button" class="btn-secondary text-sm">Cancelar</button>
                            <button @click="confirm" :disabled="loading" type="button" class="btn-danger text-sm">
                                <span x-show="!loading">Confirmar</span>
                                <span x-show="loading">Procesando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Session Toast Notifications -->
        @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.toast.success(`{{ session('success') }}`);
            });
        </script>
        @endif
        @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.toast.error(`{{ session('error') }}`);
            });
        </script>
        @endif

        @stack('modals')
        @livewireScripts
    </body>
</html>
