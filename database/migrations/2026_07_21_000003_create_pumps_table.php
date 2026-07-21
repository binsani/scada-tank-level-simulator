<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pumps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tank_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->default('Main Pump');
            $table->string('state')->default('stopped');
            $table->decimal('running_hours', 10, 3)->default(0);
            $table->decimal('motor_current_a', 5, 2)->default(7.2);
            $table->decimal('motor_temperature_c', 5, 2)->default(47.8);
            $table->decimal('energy_used_kwh', 10, 3)->default(0);
            $table->decimal('efficiency_percent', 5, 2)->default(86.1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pumps');
    }
};
