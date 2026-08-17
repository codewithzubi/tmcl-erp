<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// End Slaughter's Chiller Transfer/Allocation section (tomcl ops scope doc
// pages 16-17) — one entry per chiller involved in this Slaughter ID, since
// the chiller/tag assignment itself already happened at Carcass Weight
// time. Replaces the old per-department chiller/blast-freezer/boti/boneless
// transfer quantity columns, which are left in place (unused going forward)
// so already-completed slaughters keep their historical figures.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->json('chiller_transfers')->nullable()->after('re_weight_entries');
        });
    }

    public function down(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->dropColumn('chiller_transfers');
        });
    }
};
