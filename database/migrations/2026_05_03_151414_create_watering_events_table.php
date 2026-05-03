<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('watering_events', function (Blueprint $table) {
            $table->id('watering_id');
            $table->foreignId('plant_id')->constrained('plants', 'plant_id')->cascadeOnDelete();
            $table->timestamp('watered_at');
            $table->enum('source', ['button', 'manual_app', 'scheduled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watering_events');
    }
};
