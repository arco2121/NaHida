<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ButtonPressed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $plantId;
    public string $message;

    public function __construct(int $plantId, string $message)
    {
        $this->plantId = $plantId;
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        // Canale specifico per pianta, così il browser ascolta solo la sua
        return [
            new Channel("plant.{$this->plantId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ButtonPressed';
    }
}
