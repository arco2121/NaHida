<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SensorReadingSeeder extends Seeder
{
    public function run(): void
    {
        $plants = DB::table('plants')->pluck('plant_id');

        foreach ($plants as $plantId) {
            for ($i = 0; $i < 5; $i++) {
                DB::table('sensor_readings')->insert([
                    'plant_id'      => $plantId,
                    'humidity'      => rand(40, 70) + 0.5,
                    'temperature'   => rand(18, 28) + 0.5,
                    'soil_humidity' => rand(30, 60) + 0.5,
                    'luminosity'    => null,
                    'recorded_at'   => now()->subMinutes($i * 10),
                ]);
            }
        }
    }
}
