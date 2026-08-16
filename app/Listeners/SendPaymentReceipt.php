<?php

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use Illuminate\Support\Facades\Log;

class SendPaymentReceipt
{
    /**
     * Handle the event.
     */
    public function handle(PaymentConfirmed $event): void
    {
        $payment = $event->payment;
        $client = $payment->cliente;

        // TODO: Implement email/WhatsApp receipt sending
        Log::info('Payment receipt', [
            'payment_id' => $payment->id,
            'client' => $client->nombre . ' ' . $client->apellido,
            'amount' => $payment->abonado,
        ]);
    }
}
