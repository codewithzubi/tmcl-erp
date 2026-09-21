<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            // Blast Freezer is Chiller's sibling storage destination
            // (requirements doc item 11) — same shape as chiller_name/
            // piece_chillers, a separate column pair since a piece is
            // assigned to only one of the two at a time.
            $table->string('blast_freezer_name')->nullable()->after('chiller_name');
            $table->json('piece_blast_freezers')->nullable()->after('piece_chillers');
        });
    }

    public function down(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            $table->dropColumn(['blast_freezer_name', 'piece_blast_freezers']);
        });
    }
};
