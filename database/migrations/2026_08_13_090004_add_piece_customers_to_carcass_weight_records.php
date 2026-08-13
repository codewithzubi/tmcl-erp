<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            // Per-piece customer assignment (tomcl ops scope doc, page 6) —
            // maps a piece code (FQ1/FQ2/HQ1/HQ2 or FQ/HQ) to a customer id,
            // e.g. {"FQ1": 3, "FQ2": 3, "HQ1": 7, "HQ2": 7}. Kept as its own
            // JSON map rather than splitting into one row per piece, since
            // every other feature (weights, approval, lock) still treats one
            // Carcass Weight record as one whole animal.
            $table->json('piece_customers')->nullable()->after('cut_type');
        });
    }

    public function down(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            $table->dropColumn('piece_customers');
        });
    }
};
