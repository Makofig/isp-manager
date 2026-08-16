<?php

namespace App\Events;

use App\Models\Client;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientBanned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Client $client;
    public string $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(Client $client, string $reason = 'Non-payment')
    {
        $this->client = $client;
        $this->reason = $reason;
    }
}
