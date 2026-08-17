<?php

namespace App\Console\Commands;

use App\Services\QuotaGenerationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyQuotas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-monthly-quotas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly quota for current month (first day of month at 00:05)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = (int) Carbon::now()->year;
        $month = (int) Carbon::now()->month;

        $this->info("Generating quota for {$month}/{$year}...");

        $result = app(QuotaGenerationService::class)->handle($year, $month);

        if ($result['created']) {
            $this->info("✅ {$result['message']}");
        } else {
            $this->warn("⚠️ {$result['message']}");
        }

        return Command::SUCCESS;
    }
}
