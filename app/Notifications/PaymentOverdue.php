<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentOverdue extends Notification
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
        return ['database'];
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
