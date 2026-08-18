<?php

namespace App\Notifications;

use App\Mail\PaymentOverdueMail;
use App\Models\Payments;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class PaymentOverdue extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public int $clientId,
        public int $paymentId,
        public int $daysOverdue,
        public float $amount
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
        $payment = Payments::with('clients')->find($this->paymentId);

        return (new PaymentOverdueMail($payment, $this->daysOverdue))
            ->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'client_id' => $this->clientId,
            'payment_id' => $this->paymentId,
            'days_overdue' => $this->daysOverdue,
            'amount' => $this->amount,
        ];
    }
}
