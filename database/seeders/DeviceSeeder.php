<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $plants = DB::table('plants')->pluck('plant_id');

        foreach ($plants as $plantId) {
            DB::table('devices')->insert([
                'plant_id'     => $plantId,
                'device_token' => substr(md5((string)$plantId), 0, 8), // token finto tipo "a3f9bc12"
                'last_seen_at' => now()->subMinutes(rand(1, 60)),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
}
