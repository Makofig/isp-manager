<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Client;
use App\Models\Payments;
use App\Models\Quota;
use App\Events\QuotaProgressUpdated;

class GenerateQuotaPayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $quotaId;

    public int $timeout = 300;

    public function __construct($quotaId)
    {
        $this->quotaId = $quotaId;
    }

    public function handle(): void
    {
        $quota = Quota::find($this->quotaId);
        if (!$quota) {
            return;
        }

        $quotaMonth = $quota->created_at->month;
        $total = Client::count();
        $processed = 0;
        $batch = [];
        $batchSize = 200;

        Client::with('contract')
            ->chunk($batchSize, function ($clients) use ($total, &$processed, &$batch, $batchSize, $quotaMonth) {

                foreach ($clients as $client) {
                    if (!$client->contract) {
                        $processed++;
                        continue;
                    }

                    $batch[] = [
                        'id_cliente' => $client->id,
                        'id_cuota' => $this->quotaId,
                        'num_cuotas' => $quotaMonth,
                        'costo' => $client->contract->costo,
                        'abonado' => 0,
                        'estado' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $processed++;
                }

                if (count($batch) >= $batchSize) {
                    Payments::insert($batch);
                    $batch = [];
                }

                $progress = $total > 0 ? intval(($processed / $total) * 100) : 100;

                cache()->put(
                    "quota_progress_{$this->quotaId}",
                    $progress,
                    now()->addMinutes(30)
                );

                if ($progress % 10 === 0 || $progress >= 100) {
                    QuotaProgressUpdated::dispatch($this->quotaId, $progress);
                }
            });

        if (!empty($batch)) {
            Payments::insert($batch);
        }

        cache()->put("quota_progress_{$this->quotaId}", 100, now()->addMinutes(30));
    }
}
