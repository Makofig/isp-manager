<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Schema::defaultStringLength(191);
        // RateLimiter::for('api', function (Request $request) {
        //     return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        // });
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('auth-users', function (Request $request) {
            return Limit::perMinute(120)->by(
                optional($request->user())->id ?: $request->ip()
            );
        });

        RateLimiter::for('stats-heavy', function (Request $request) {
            return Limit::perMinute(10)->by(
                optional($request->user())->id ?: $request->ip()
            )->response(function () {
                return response()->json([
                    'message' => 'Too many requests. Please try again later.',
                ], 429);
            });
        });

        // Register UI components
        Blade::component('components.ui.toast', 'ui-toast');
        Blade::component('components.ui.confirm', 'ui-confirm');
        Blade::component('components.ui.modal', 'ui-modal');
        Blade::component('components.ui.dropdown', 'ui-dropdown');
        Blade::component('components.ui.loading', 'ui-loading');
        Blade::component('components.ui.empty-state', 'ui-empty-state');
        Blade::component('components.ui.badge', 'ui-badge');
        Blade::component('components.ui.datatable', 'ui-datatable');

        // Register layout components
        Blade::component('components.sidebar', 'sidebar');
        Blade::component('components.topbar', 'topbar');
        Blade::component('components.page-header', 'page-header');
        Blade::component('components.stat-card', 'stat-card');
        Blade::component('components.skeleton', 'skeleton');
        Blade::component('components.icon', 'icon');
        Blade::component('components.search-input', 'search-input');
        Blade::component('components.loading-overlay', 'loading-overlay');
        Blade::component('components.data-table', 'data-table');

        // Register UI form components (Fase 5 prep)
        Blade::component('components.ui.input', 'ui-input');
        Blade::component('components.ui.button', 'ui-button');
        Blade::component('components.ui.select', 'ui-select');
    }
}
