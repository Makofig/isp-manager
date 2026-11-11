<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    /**
     * Return health status for monitoring infrastructure.
     *
     * Checks: database connectivity, disk space.
     * No auth required — intended for external health checks.
     */
    public function __invoke()
    {
        $checks = [];
        $healthy = true;

        // Database connectivity check
        try {
            DB::connection()->getPdo();
            $checks['database'] = ['status' => 'ok'];
        } catch (\Exception $e) {
            $checks['database'] = ['status' => 'error', 'message' => 'Database connection failed'];
            $healthy = false;
        }

        // Disk space check
        try {
            $diskSpace = disk_free_space('/');
            // Return bytes available in GB
            $checks['disk_space'] = [
                'status' => 'ok',
                'free_gb' => round($diskSpace / (1024 ** 3), 1),
            ];
        } catch (\Exception $e) {
            $checks['disk_space'] = ['status' => 'error', 'message' => 'Unable to check disk space'];
            $healthy = false;
        }

        return response()->json([
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'checks' => $checks,
            'timestamp' => now()->toISOString(),
        ], $healthy ? 200 : 503);
    }
}
