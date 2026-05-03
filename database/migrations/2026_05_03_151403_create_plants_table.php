<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plants', function (Blueprint $table) {
            $table->id('plant_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->string('plant_name');
            $table->text('notes')->nullable();
            $table->float('hum_min');
            $table->float('hum_max');
            $table->float('temp_min');
            $table->float('temp_max');
            $table->float('soil_hum_min');
            $table->float('soil_hum_max');
            $table->enum('lum_preference', ['low', 'medium', 'high', 'direct'])->nullable();
            $table->integer('watering_cycle'); // in ore
            $table->string('plant_variant')->nullable();
            $table->string('plant_color')->nullable();
            $table->string('flower_color')->nullable();
            $table->string('pot_color')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plants');
    }
};
