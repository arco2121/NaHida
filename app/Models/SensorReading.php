<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReading extends Model
{
    protected $primaryKey = 'reading_id';

    // La tabella ha recorded_at invece di created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'plant_id',
        'humidity',
        'temperature',
        'soil_humidity',
        'luminosity',
        'recorded_at',
    ];

    protected $casts = [
        'humidity'      => 'float',
        'temperature'   => 'float',
        'soil_humidity' => 'float',
        'luminosity'    => 'float',
        'recorded_at'   => 'datetime',
    ];

    // Imposta recorded_at automaticamente al momento del salvataggio
    protected static function booted(): void
    {
        static::creating(function (SensorReading $reading) {
            $reading->recorded_at ??= now();
        });
    }

    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class, 'plant_id', 'plant_id');
    }
}
