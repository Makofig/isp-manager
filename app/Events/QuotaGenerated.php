<?php

namespace App\Events;

use App\Models\Quota;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuotaGenerated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Quota $quota;
    public int $paymentsCreated;

    /**
     * Create a new event instance.
     */
    public function __construct(Quota $quota, int $paymentsCreated)
    {
        $this->quota = $quota;
        $this->paymentsCreated = $paymentsCreated;
    }
}
