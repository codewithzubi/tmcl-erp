<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            // Per-piece chiller assignment (tomcl ops scope doc, page 7) —
            // maps a piece code to a chiller name, mirroring piece_customers.
            $table->json('piece_chillers')->nullable()->after('piece_customers');
        });
    }

    public function down(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            $table->dropColumn('piece_chillers');
        });
    }
};
