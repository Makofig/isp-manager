<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientSuspendedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Client $client;
    public string $reason;

    public function __construct(Client $client, string $reason)
    {
        $this->client = $client;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cliente suspendido - ' . config('app.name', 'InterSys'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-suspended',
            with: [
                'client' => $this->client,
                'reason' => $this->reason,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
