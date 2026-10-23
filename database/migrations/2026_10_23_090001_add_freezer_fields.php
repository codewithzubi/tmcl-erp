<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 20 Oct 2026 points 4/15 — Freezers (Setups > Freezer, Cold Storage >
// Freezers). Meat Transfer can move pieces into a Freezer (piece_freezers,
// same per-piece shape as piece_blast_freezers) and draw stock back out of
// one (freezer_name on the transfer/export, like blast_freezer_name).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            $table->json('piece_freezers')->nullable()->after('piece_blast_freezer_in_times');
            $table->json('piece_freezer_in_times')->nullable()->after('piece_freezers');
        });
        Schema::table('meat_transfer_entries', function (Blueprint $table) {
            $table->string('freezer_name')->nullable()->after('blast_freezer_name');
        });
        Schema::table('export_entries', function (Blueprint $table) {
            $table->string('freezer_name')->nullable()->after('blast_freezer_name');
        });
    }

    public function down(): void
    {
        Schema::table('export_entries', function (Blueprint $table) {
            $table->dropColumn('freezer_name');
        });
        Schema::table('meat_transfer_entries', function (Blueprint $table) {
            $table->dropColumn('freezer_name');
        });
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            $table->dropColumn(['piece_freezers', 'piece_freezer_in_times']);
        });
    }
};
