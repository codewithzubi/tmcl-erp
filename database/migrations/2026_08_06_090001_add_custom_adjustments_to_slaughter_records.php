<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Labeled weight adjustments applied during End Slaughter (e.g. "Skin Fat: -10kg")
// — stored as a JSON array of {title, amount} so each adjustment's reason is
// kept, not just a single anonymous net number.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->json('custom_adjustments')->nullable()->after('final_weight');
        });
    }

    public function down(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->dropColumn('custom_adjustments');
        });
    }
};
