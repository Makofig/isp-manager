<?php

namespace App\Events;

use App\Models\Payments;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Payments $payment;

    /**
     * Create a new event instance.
     */
    public function __construct(Payments $payment)
    {
        $this->payment = $payment;
    }
}
