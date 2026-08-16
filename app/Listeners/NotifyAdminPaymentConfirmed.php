<?php

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use Illuminate\Support\Facades\Log;

class NotifyAdminPaymentConfirmed
{
    /**
     * Handle the event.
     */
    public function handle(PaymentConfirmed $event): void
    {
        $payment = $event->payment;

        // TODO: Send real-time notification to admin dashboard
        Log::info('Admin notified of payment', [
            'payment_id' => $payment->id,
            'amount' => $payment->abonado,
        ]);
    }
}
