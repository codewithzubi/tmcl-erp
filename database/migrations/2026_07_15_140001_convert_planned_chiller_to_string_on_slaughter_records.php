<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->dropForeign(['planned_chiller_id']);
        });

        DB::statement('ALTER TABLE slaughter_records CHANGE planned_chiller_id planned_chiller VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement('UPDATE slaughter_records SET planned_chiller = NULL');
        DB::statement('ALTER TABLE slaughter_records CHANGE planned_chiller planned_chiller_id BIGINT UNSIGNED NULL');

        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->foreign('planned_chiller_id')->references('id')->on('storage_units')->nullOnDelete();
        });
    }
};
