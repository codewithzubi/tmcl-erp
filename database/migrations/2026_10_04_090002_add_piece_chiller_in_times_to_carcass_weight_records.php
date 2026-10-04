<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            $table->json('piece_chiller_in_times')->nullable()->after('piece_chillers');
        });
    }

    public function down(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            $table->dropColumn('piece_chiller_in_times');
        });
    }
};
