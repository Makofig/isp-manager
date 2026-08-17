<?php

namespace App\Notifications;

use App\Models\Quota;
use App\Mail\QuotaGeneratedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class QuotaGenerated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Quota $quota,
        public int $clientCount
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
        return (new QuotaGeneratedMail($this->quota, $this->clientCount))
            ->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'quota_id' => $this->quota->id,
            'month_year' => $this->quota->numero,
            'client_count' => $this->clientCount,
        ];
    }
}
