<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WateringEventSeeder extends Seeder
{
    public function run(): void
    {
        $plants = DB::table('plants')->pluck('plant_id');

        foreach ($plants as $plantId) {
            for ($i = 0; $i < 3; $i++) {
                DB::table('watering_events')->insert([
                    'plant_id'   => $plantId,
                    'watered_at' => now()->subHours($i * 24),
                    'source'     => 'button',
                ]);
            }
        }
    }
}
