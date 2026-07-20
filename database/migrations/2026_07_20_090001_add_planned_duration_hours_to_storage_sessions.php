<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('storage_sessions', function (Blueprint $table) {
            $table->unsignedInteger('planned_duration_hours')->nullable()->after('time_out');
        });
    }

    public function down(): void
    {
        Schema::table('storage_sessions', function (Blueprint $table) {
            $table->dropColumn('planned_duration_hours');
        });
    }
};
