<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WateringEvent extends Model
{
    protected $primaryKey = 'watering_id';

    public $timestamps = false;

    protected $fillable = [
        'plant_id',
        'watered_at',
        'source',
    ];

    protected $casts = [
        'watered_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (WateringEvent $event) {
            $event->watered_at ??= now();
        });
    }

    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class, 'plant_id', 'plant_id');
    }
}
