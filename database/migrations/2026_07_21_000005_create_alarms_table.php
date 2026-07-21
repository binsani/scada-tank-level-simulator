<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alarms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tank_id')->nullable()->constrained()->nullOnDelete();
            $table->string('priority')->default('low');
            $table->string('message');
            $table->string('equipment')->nullable();
            $table->string('status')->default('active');
            $table->string('acknowledged_by')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('triggered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alarms');
    }
};
