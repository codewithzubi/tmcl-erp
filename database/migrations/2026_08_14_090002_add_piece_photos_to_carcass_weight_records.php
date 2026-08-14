<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            // One photo per piece (FQ1/FQ2/HQ1/HQ2 or FQ/HQ) instead of one
            // per whole animal — ops scope doc page 9, item 18. photo_path
            // stays as-is for backward compatibility / general thumbnail use.
            $table->json('piece_photos')->nullable()->after('photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            $table->dropColumn('piece_photos');
        });
    }
};
