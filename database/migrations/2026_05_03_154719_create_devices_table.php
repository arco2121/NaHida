<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id('device_id');
            $table->unsignedBigInteger('plant_id')->nullable(); // nullable: il device può esistere senza pianta associata
            $table->foreign('plant_id')->references('plant_id')->on('plants')->nullOnDelete();
            $table->string('device_token')->unique(); // es. "a3f9bc"
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
