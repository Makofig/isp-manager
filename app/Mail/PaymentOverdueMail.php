<?php

namespace App\Mail;

use App\Models\Payments;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentOverdueMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Payments $payment;
    public int $daysOverdue;

    public function __construct(Payments $payment, int $daysOverdue)
    {
        $this->payment = $payment;
        $this->daysOverdue = $daysOverdue;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pago vencido - {$this->daysOverdue} días de atraso",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-overdue',
            with: [
                'payment' => $this->payment,
                'daysOverdue' => $this->daysOverdue,
                'client' => $this->payment->clients,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
