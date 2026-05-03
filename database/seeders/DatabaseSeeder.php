<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PlantSeeder::class,
            DeviceSeeder::class,
            SensorReadingSeeder::class,
            WateringEventSeeder::class,
        ]);
    }
}
