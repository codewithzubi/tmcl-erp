<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 20 Oct feedback, points 12–13 — Boti/Boneless department transfers record
// the cartons produced, their total weight and the resulting yield
// (carton weight / weight * 100). Cartons is decimal because a line spanning
// several Slaughter IDs is split across them in proportion to weight.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meat_transfer_entries', function (Blueprint $table) {
            $table->decimal('cartons', 10, 2)->nullable()->after('quantity');
            $table->decimal('carton_weight', 12, 2)->nullable()->after('cartons');
            $table->decimal('yield_percent', 7, 2)->nullable()->after('carton_weight');
        });
    }

    public function down(): void
    {
        Schema::table('meat_transfer_entries', function (Blueprint $table) {
            $table->dropColumn(['cartons', 'carton_weight', 'yield_percent']);
        });
    }
};
