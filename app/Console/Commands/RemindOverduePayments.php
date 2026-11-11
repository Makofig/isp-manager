<?php

namespace App\Console\Commands;

use App\Models\Payments;
use App\Models\User;
use App\Notifications\PaymentOverdue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RemindOverduePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:remind-overdue {--days=30 : Minimum days overdue to trigger notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect overdue payments and send notifications to admins';

    /**
     * Execute the console command.
     *
     * Finds payments that are unpaid and past the due date,
     * then dispatches PaymentOverdue notification to admin users.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');

        // Find unpaid payments older than N days
        $overduePayments = Payments::where('estado', 0)
            ->whereNull('fecha_pago')
            ->where('created_at', '<', now()->subDays($days))
            ->with('clients')
            ->get();

        if ($overduePayments->isEmpty()) {
            $this->info("No se encontraron pagos vencidos con más de {$days} días.");

            return Command::SUCCESS;
        }

        $count = 0;

        foreach ($overduePayments as $payment) {
            $daysOverdue = (int) $payment->created_at->diffInDays(now());

            // Notify only admin users
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new PaymentOverdue(
                    clientId: $payment->id_cliente,
                    paymentId: $payment->id,
                    daysOverdue: $daysOverdue,
                    amount: $payment->costo
                ));
            }

            $count++;

            $clientName = $payment->clients->nombre ?? 'Desconocido';
            $this->info("Notificado: {$clientName} - {$daysOverdue} días - \${$payment->costo}");
        }

        Log::info('Payment overdue notifications sent', [
            'count' => $count,
            'min_days' => $days,
        ]);

        $this->info("Total: {$count} pagos vencidos notificados.");

        return Command::SUCCESS;
    }
}
