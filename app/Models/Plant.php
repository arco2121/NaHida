<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Plant extends Model
{
    protected $primaryKey = 'plant_id';

    protected $fillable = [
        'user_id',
        'plant_name',
        'notes',
        'hum_min',
        'hum_max',
        'temp_min',
        'temp_max',
        'soil_hum_min',
        'soil_hum_max',
        'lux_min',
        'lux_max',
        'lum_preference',
        'watering_cycle',
        'plant_variant',
        'plant_color',
        'flower_color',
        'pot_color',
    ];

    protected $casts = [
        'hum_min'        => 'float',
        'hum_max'        => 'float',
        'temp_min'       => 'float',
        'temp_max'       => 'float',
        'soil_hum_min'   => 'float',
        'soil_hum_max'   => 'float',
        'watering_cycle' => 'integer',
        'plant_variant'  => 'integer',
        'plant_color'    => 'integer',
        'flower_color'   => 'integer',
        'pot_color'      => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function device(): HasOne
    {
        return $this->hasOne(Device::class, 'plant_id', 'plant_id');
    }

    public function sensorReadings(): HasMany
    {
        return $this->hasMany(SensorReading::class, 'plant_id', 'plant_id');
    }

    public function wateringEvents(): HasMany
    {
        return $this->hasMany(WateringEvent::class, 'plant_id', 'plant_id');
    }
}
