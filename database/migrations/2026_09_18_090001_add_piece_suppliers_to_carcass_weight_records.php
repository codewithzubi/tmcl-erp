<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            // Per-piece supplier assignment (requirements doc item 16) —
            // maps a piece code to a supplier id, mirroring piece_customers.
            // No record-level supplier_id column exists (unlike customer_id)
            // — the parent Slaughter's own supplier_ids is the fallback.
            $table->json('piece_suppliers')->nullable()->after('piece_customers');
        });
    }

    public function down(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            $table->dropColumn('piece_suppliers');
        });
    }
};
