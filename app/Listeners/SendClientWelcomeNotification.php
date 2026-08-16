<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use Illuminate\Support\Facades\Log;

class SendClientWelcomeNotification
{
    /**
     * Handle the event.
     */
    public function handle(ClientCreated $event): void
    {
        $client = $event->client;

        // TODO: Send welcome email/WhatsApp with service details
        Log::info('Client welcome notification', [
            'client_id' => $client->id,
            'name' => $client->nombre . ' ' . $client->apellido,
        ]);
    }
}
