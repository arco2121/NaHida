<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SensorUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int        $plantId,
        public float|null $humidity,
        public float|null $temperature,
        public float|null $soil_humidity,
        public float|null $luminosity,
        public string     $recordedAt,
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel("plant.{$this->plantId}")];
    }

    public function broadcastAs(): string
    {
        return 'SensorUpdated';
    }
}
