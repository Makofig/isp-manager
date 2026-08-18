<?php

use App\Console\Commands\RemindOverduePayments;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('quotas:generate', function () {
    $this->call('app:generate-monthly-quotas');
})->describe('Generate monthly quotas for clients');

Artisan::command('payments:remind-overdue', function () {
    $this->call(RemindOverduePayments::class);
})->describe('Send notifications for overdue payments');

// Scheduler: run on 1st of each month at 00:05
Schedule::command('app:generate-monthly-quotas')->monthlyOn(1, '00:05');

// Scheduler: check overdue payments daily at 08:00
Schedule::command('payments:remind-overdue --days=30')->dailyAt('08:00');