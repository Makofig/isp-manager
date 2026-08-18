<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>InterSys — Network Management</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes pulse-glow {
                0%, 100% { box-shadow: 0 0 20px rgba(88, 166, 255, 0.3); }
                50% { box-shadow: 0 0 40px rgba(88, 166, 255, 0.6); }
            }
            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-10px); }
            }
            .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
            .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
            .animate-pulse-glow { animation: pulse-glow 3s ease-in-out infinite; }
            .animate-float { animation: float 6s ease-in-out infinite; }
            .delay-100 { animation-delay: 0.1s; }
            .delay-200 { animation-delay: 0.2s; }
            .delay-300 { animation-delay: 0.3s; }
            .delay-400 { animation-delay: 0.4s; }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-dark-bg text-text-primary flex flex-col">
            <!-- Header / Nav -->
            <header class="w-full px-6 lg:px-12 py-5 flex items-center justify-between animate-fade-in">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-accent/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-text-primary tracking-wide">INTERSYS</span>
                        <span class="text-[10px] text-muted block tracking-widest uppercase">Network Management</span>
                    </div>
                </div>

                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary text-sm">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary text-sm animate-pulse-glow">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-ghost text-sm">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            </header>

            <!-- Main Content -->
            <main class="flex-1 flex items-center justify-center px-6 lg:px-12 py-12">
                <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                    <!-- Left: Info -->
                    <div class="space-y-8 animate-fade-in-up opacity-0 delay-100">
                        <div>
                            <h1 class="text-3xl lg:text-4xl font-bold text-text-primary leading-tight">
                                Administrá tu red
                                <span class="text-accent">profesionalmente</span>
                            </h1>
                            <p class="mt-4 text-muted-light text-base leading-relaxed max-w-md">
                                Gestión de clientes, contratos, cuotas y puntos de acceso. 
                                Todo en una plataforma moderna, rápida y confiable.
                            </p>
                        </div>

                        <!-- Features -->
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3 animate-fade-in-up opacity-0 delay-200">
                                <div class="w-6 h-6 rounded-[6px] bg-success/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-text-primary">Gestión de Clientes</span>
                                    <p class="text-xs text-muted mt-0.5">Administra clientes, banneos y deudores</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3 animate-fade-in-up opacity-0 delay-300">
                                <div class="w-6 h-6 rounded-[6px] bg-accent/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-text-primary">Control de Cuotas</span>
                                    <p class="text-xs text-muted mt-0.5">Generación automática y seguimiento de pagos</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3 animate-fade-in-up opacity-0 delay-400">
                                <div class="w-6 h-6 rounded-[6px] bg-warning/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-text-primary">Puntos de Acceso</span>
                                    <p class="text-xs text-muted mt-0.5">Monitoreo de APs y clientes conectados</p>
                                </div>
                            </li>
                        </ul>

                        <!-- CTA -->
                        <div class="flex items-center gap-3 animate-fade-in-up opacity-0 delay-400">
                            <a href="{{ route('login') }}" class="btn-primary text-sm">
                                Comenzar ahora
                            </a>
                            <a href="{{ route('clients') }}" class="btn-ghost text-sm">
                                Ver clientes
                            </a>
                        </div>
                    </div>

                    <!-- Right: Visual -->
                    <div class="relative flex items-center justify-center animate-fade-in opacity-0 delay-200">
                        <div class="relative w-full max-w-md aspect-square animate-float">
                            <!-- Glow background -->
                            <div class="absolute inset-0 bg-accent/5 rounded-full blur-3xl"></div>
                            <!-- Logo container -->
                            <div class="relative w-full h-full rounded-[16px] border border-dark-border bg-dark-surface flex items-center justify-center overflow-hidden">
                                <img src="{{ asset('images/logo.png') }}" alt="InterSys Logo" class="w-3/4 h-3/4 object-contain" />
                            </div>
                            <!-- Decorative dots -->
                            <div class="absolute -top-4 -right-4 w-3 h-3 rounded-full bg-accent/40"></div>
                            <div class="absolute -bottom-6 -left-6 w-4 h-4 rounded-full bg-success/30"></div>
                            <div class="absolute top-1/2 -right-8 w-2 h-2 rounded-full bg-warning/50"></div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="px-6 lg:px-12 py-6 border-t border-dark-border">
                <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
                    <span class="text-xs text-muted">&copy; {{ date('Y') }} InterSys. Network Management Platform.</span>
                    <div class="flex items-center gap-4">
                        <span class="text-xs text-muted">Laravel 12</span>
                        <span class="text-xs text-muted">•</span>
                        <span class="text-xs text-muted">Livewire 3</span>
                        <span class="text-xs text-muted">•</span>
                        <span class="text-xs text-muted">Tailwind CSS</span>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
