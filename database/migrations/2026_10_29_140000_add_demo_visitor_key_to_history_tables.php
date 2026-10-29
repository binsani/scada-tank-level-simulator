<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['events', 'alarms', 'trend_data'] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                // Null preserves existing legacy shared-demo records.
                $blueprint->string('demo_visitor_key', 64)->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['events', 'alarms', 'trend_data'] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->dropIndex(['demo_visitor_key']);
                $blueprint->dropColumn('demo_visitor_key');
            });
        }
    }
};
