<?php

namespace App\Services;

use App\Jobs\GenerateQuotaPayments;
use App\Models\Client;
use App\Models\Quota;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class QuotaGenerationService
{
    /**
     * Generate the monthly quota for the given year and month.
     *
     * Business rules:
     * - Only ONE quota per month (enforced by unique constraint)
     * - Quota must exist BEFORE generating payments
     * - If quota already exists, no action is taken
     *
     * @param int $year The year (e.g. 2025)
     * @param int $month The month (1-12)
     * @return array{created: bool, quota: Quota|null, message: string}
     * @throws InvalidArgumentException If month is invalid
     */
    public function handle(int $year, int $month): array
    {
        // Validate month range
        if ($month < 1 || $month > 12) {
            throw new InvalidArgumentException("Mes inválido: {$month}. Debe ser entre 1 y 12.");
        }

        return DB::transaction(function () use ($year, $month) {
            // Check if quota already exists for this month and year
            // lockForUpdate prevents race conditions
            $existing = Quota::where('numero', $month)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                Log::info("Cuota ya existe para mes {$month}/{$year}", [
                    'quota_id' => $existing->id,
                ]);

                return [
                    'created' => false,
                    'quota' => $existing,
                    'message' => "La cuota ya existe para el período {$month}/{$year}.",
                ];
            }

            // Create the quota FIRST (before any payments)
            $quota = Quota::create([
                'numero' => $month,
                'year' => $year,
            ]);

            Log::info("Cuota creada para mes {$month}/{$year}", [
                'quota_id' => $quota->id,
            ]);

            // Count clients for notification payload
            $clientCount = Client::count();

            // Dispatch job to generate payments in chunks
            // This keeps the HTTP request fast and handles large client lists
            GenerateQuotaPayments::dispatch($quota->id, $clientCount);

            return [
                'created' => true,
                'quota' => $quota,
                'message' => "Cuota #{$quota->id} generada exitosamente para {$month}/{$year}. Pagos en proceso.",
            ];
        });
    }
}
