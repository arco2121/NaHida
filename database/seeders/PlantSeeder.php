<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlantSeeder extends Seeder
{
    public function run(): void
    {
        $users = DB::table('users')->pluck('user_id');

        foreach ($users as $userId) {
            DB::table('plants')->insert([
                'user_id'       => $userId,
                'plant_name'    => 'Pianta di test',
                'notes'         => 'Pianta generata dal seeder',
                'hum_min'       => 40.0,
                'hum_max'       => 70.0,
                'temp_min'      => 18.0,
                'temp_max'      => 28.0,
                'soil_hum_min'  => 30.0,
                'soil_hum_max'  => 60.0,
                'lum_preference'=> 'medium',
                'watering_cycle'=> 48,
                'plant_variant' => null,
                'plant_color'   => null,
                'flower_color'  => null,
                'pot_color'     => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
