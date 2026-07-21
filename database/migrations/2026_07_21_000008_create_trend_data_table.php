<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trend_data', function (Blueprint $table): void {
            $table->id();
            $table->decimal('tank_level', 5, 2)->default(0);
            $table->decimal('pressure', 5, 2)->default(0);
            $table->decimal('temperature', 5, 2)->default(0);
            $table->decimal('flow_rate', 8, 2)->default(0);
            $table->decimal('motor_current', 5, 2)->default(0);
            $table->decimal('energy_consumption', 10, 3)->default(0);
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trend_data');
    }
};
