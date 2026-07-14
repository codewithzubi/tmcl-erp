<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Additive quality-check fields from the DocScanner "Loading/Shipment"
    // wireframe, layered on top of the existing Scope Doc dispatches table.
    public function up(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->decimal('ph_level', 5, 2)->nullable()->after('status');
            $table->string('cloth_check')->nullable()->after('ph_level');
            $table->decimal('temperature', 5, 2)->nullable()->after('cloth_check');
            $table->string('label_check')->nullable()->after('temperature');
        });
    }

    public function down(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropColumn(['ph_level', 'cloth_check', 'temperature', 'label_check']);
        });
    }
};
