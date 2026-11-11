<?php

namespace App\Mail;

use App\Models\Quota;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuotaGeneratedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Quota $quota;
    public int $clientCount;

    /**
     * Create a new message instance.
     */
    public function __construct(Quota $quota, int $clientCount)
    {
        $this->quota = $quota;
        $this->clientCount = $clientCount;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Cuota generada - {$this->quota->created_at->format('F Y')}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.quota-generated',
            with: [
                'quota' => $this->quota,
                'clientCount' => $this->clientCount,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
