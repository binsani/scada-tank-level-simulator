<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pump_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tank_id')->nullable()->constrained()->nullOnDelete();
            $table->string('pump_state');
            $table->decimal('motor_current_a', 5, 2)->default(0);
            $table->decimal('motor_temperature_c', 5, 2)->default(0);
            $table->decimal('energy_used_kwh', 10, 3)->default(0);
            $table->timestamp('logged_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pump_logs');
    }
};
