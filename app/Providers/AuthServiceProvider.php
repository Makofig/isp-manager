<?php

namespace App\Providers;

use App\Models\Access_Point;
use App\Models\Client;
use App\Models\Contracts;
use App\Models\Payments;
use App\Models\Quota;
use App\Policies\AccessPointPolicy;
use App\Policies\ClientPolicy;
use App\Policies\ContractPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\QuotaPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @ var array<class-string, class-string>
     */
    protected $policies = [
        Client::class => ClientPolicy::class,
        Contracts::class => ContractPolicy::class,
        Access_Point::class => AccessPointPolicy::class,
        Payments::class => PaymentPolicy::class,
        Quota::class => QuotaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gates globales
        Gate::before(function ($user, $ability) {
            // Admin siempre tiene acceso total
            if ($user->isAdmin()) {
                return true;
            }
        });

        // Gate para acceder al dashboard/estadísticas
        Gate::define('view-stats', function ($user) {
            return in_array($user->role, ['admin', 'operator', 'auditor']);
        });

        // Gate para exportar PDFs
        Gate::define('export-pdf', function ($user) {
            return in_array($user->role, ['admin', 'operator', 'auditor']);
        });
    }
}
