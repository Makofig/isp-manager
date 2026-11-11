<?php

namespace App\Notifications;

use App\Mail\ClientSuspendedMail;
use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class ClientSuspended extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public int $clientId,
        public string $reason,
        public string $suspendedAt
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): Mailable
    {
        $client = Client::find($this->clientId);

        return (new ClientSuspendedMail($client, $this->reason))
            ->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'client_id' => $this->clientId,
            'reason' => $this->reason,
            'suspended_at' => $this->suspendedAt,
        ];
    }
}
