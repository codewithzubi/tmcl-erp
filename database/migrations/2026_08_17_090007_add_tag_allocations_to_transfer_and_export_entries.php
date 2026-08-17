<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tag-Level Traceability (Meat Transfer/Loading/Export scope doc, page 8,
// item 15) — even a quantity-only transfer keeps a breakdown of exactly
// which Tag IDs (and how much weight from each) it drew from, so per-tag
// remaining weight can always be computed instead of drifting from the
// chiller-level total.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meat_transfer_entries', function (Blueprint $table) {
            $table->json('tag_allocations')->nullable()->after('quantity');
        });
        Schema::table('export_entries', function (Blueprint $table) {
            $table->json('tag_allocations')->nullable()->after('export_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('meat_transfer_entries', function (Blueprint $table) {
            $table->dropColumn('tag_allocations');
        });
        Schema::table('export_entries', function (Blueprint $table) {
            $table->dropColumn('tag_allocations');
        });
    }
};
