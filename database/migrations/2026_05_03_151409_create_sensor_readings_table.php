<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id('reading_id');
            $table->foreignId('plant_id')->constrained('plants', 'plant_id')->cascadeOnDelete();
            $table->float('humidity');
            $table->float('temperature');
            $table->float('soil_humidity');
            $table->float('luminosity')->nullable();
            $table->timestamp('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
