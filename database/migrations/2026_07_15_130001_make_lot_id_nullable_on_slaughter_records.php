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
            $table->dropForeign(['lot_id']);
        });

        DB::statement('ALTER TABLE slaughter_records MODIFY lot_id BIGINT UNSIGNED NULL');

        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->foreign('lot_id')->references('id')->on('lots')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->dropForeign(['lot_id']);
        });

        DB::statement('ALTER TABLE slaughter_records MODIFY lot_id BIGINT UNSIGNED NOT NULL');

        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->foreign('lot_id')->references('id')->on('lots')->restrictOnDelete();
        });
    }
};
