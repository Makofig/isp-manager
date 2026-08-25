<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Quota;
use App\Jobs\GenerateQuotaPayments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuotaGeneratedMail;

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
    protected $description = 'Command to generate monthly quotas for clients';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');

        $existe = Quota::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->first();

        if ($existe) {
            $this->warn("⚠️ Fee already exists for {$year}-{$month}");
            Log::warning('quotas:generate skipped - quota already exists', [
                'year' => $year,
                'month' => $month,
            ]);
            return Command::SUCCESS;
        }

        $quota = Quota::create([
            'numero' => Carbon::now()->month,
        ]);

        GenerateQuotaPayments::dispatch($quota->id);

        $recipients = explode(',', env('QUOTA_NOTIFICATION_EMAILS', ''));
        foreach ($recipients as $email) {
            $email = trim($email);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($email)->queue(new QuotaGeneratedMail($quota, 'success'));
            }
        }

        $this->info("✅ Quota dispatched for {$year}-{$month}. Job processing in background.");
        Log::info('quotas:generate dispatched quota', [
            'quota_id' => $quota->id,
            'year' => $year,
            'month' => $month,
        ]);

        return Command::SUCCESS;
    }
}
