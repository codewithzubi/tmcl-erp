<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Requirements doc item 46 — Meat Transfer/Export need to draw from a Blast
// Freezer, not only a Chiller, so the new "Blast Freezer Tracking" tab in
// Cold Storage Management can compute a real Weight Removed figure the same
// way Chiller Tracking already does. chiller_name becomes nullable since a
// row now belongs to exactly one of the two (never both).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meat_transfer_entries', function (Blueprint $table) {
            $table->string('chiller_name')->nullable()->change();
            $table->string('blast_freezer_name')->nullable()->after('chiller_name');
        });
        Schema::table('export_entries', function (Blueprint $table) {
            $table->string('chiller_name')->nullable()->change();
            $table->string('blast_freezer_name')->nullable()->after('chiller_name');
        });
    }

    public function down(): void
    {
        Schema::table('meat_transfer_entries', function (Blueprint $table) {
            $table->dropColumn('blast_freezer_name');
            $table->string('chiller_name')->nullable(false)->change();
        });
        Schema::table('export_entries', function (Blueprint $table) {
            $table->dropColumn('blast_freezer_name');
            $table->string('chiller_name')->nullable(false)->change();
        });
    }
};
