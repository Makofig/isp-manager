<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuotaProgressUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $quotaId;
    public $progress;

    public function __construct($quotaId, $progress)
    {
        $this->quotaId = $quotaId;
        $this->progress = $progress;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('quota.' . $this->quotaId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'progress.updated';
    }
}
