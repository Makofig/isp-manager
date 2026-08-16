<?php

namespace App\Listeners;

use App\Events\ClientBanned;
use Illuminate\Support\Facades\Log;

class HandleClientBan
{
    /**
     * Handle the event.
     */
    public function handle(ClientBanned $event): void
    {
        $client = $event->client;

        // TODO: Trigger network disconnection (e.g., RADIUS CoA, API call to router)
        Log::warning('Client banned - service should be disconnected', [
            'client_id' => $client->id,
            'ip' => $client->ip,
            'reason' => $event->reason,
        ]);
    }
}
