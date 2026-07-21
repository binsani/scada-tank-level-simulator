<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tanks', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('capacity_liters')->default(1000);
            $table->decimal('level_percent', 5, 2)->default(35);
            $table->decimal('temperature_c', 5, 2)->default(27.3);
            $table->decimal('pressure_bar', 5, 2)->default(1.8);
            $table->decimal('flow_rate_l_min', 8, 2)->default(18.4);
            $table->string('operating_mode')->default('auto');
            $table->string('system_status')->default('simulation');
            $table->boolean('is_overflowing')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tanks');
    }
};
