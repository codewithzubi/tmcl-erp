<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Requirements doc item 8 (Animal Selection) — a record-level supplier,
// mirroring customer_id: the user picks it per-animal from a dropdown
// restricted to the parent Slaughter's own selected suppliers, and it's
// the fallback piece_suppliers falls back to when a piece has no override.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
        });
    }
};
