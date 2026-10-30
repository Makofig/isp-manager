<?php

namespace App\Jobs;

use App\Models\Client;
use App\Models\Quota;
use App\Models\User;
use App\Notifications\QuotaGenerated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class GenerateQuotaPayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param int $quotaId The ID of the quota to generate payments for
     * @param int $clientCount Total number of clients (for notification payload)
     */
    public function __construct(
        public int $quotaId,
        public int $clientCount
    ) {}

    /**
     * Execute the job.
     *
     * Creates one payment per client for the given quota,
     * then dispatches the QuotaGenerated notification.
     */
    public function handle(): void
    {
        $processed = 0;
        $quota = Quota::find($this->quotaId);

        if (! $quota) {
            return;
        }

        // Chunked processing for large client lists
        Client::with('contract')
            ->chunk(100, function ($clients) use (&$processed) {
                foreach ($clients as $client) {
                    \App\Models\Payments::create([
                        'id_cliente' => $client->id,
                        'id_cuota' => $this->quotaId,
                        'num_cuotas' => now()->month,
                        'costo' => $client->contract?->costo ?? 0,
                        'abonado' => 0,
                        'estado' => 0,
                        'fecha_pago' => null,
                        'comentario' => null,
                    ]);

                    $processed++;
                }

                // Update progress cache for polling UI
                $progress = $this->clientCount > 0
                    ? intval(($processed / $this->clientCount) * 100)
                    : 100;

                Cache::put(
                    "quota_progress_{$this->quotaId}",
                    $progress,
                    now()->addMinutes(30)
                );
            });

        // Mark job as complete
        Cache::put("quota_progress_{$this->quotaId}", 100);

        // Dispatch notification to admin users only
        $adminUser = User::where('role', 'admin')->first();
        if ($adminUser) {
            $adminUser->notify(new QuotaGenerated($quota, $this->clientCount));
        }
    }
}
