<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// End Slaughter's Blast Freezer Transfer/Allocation section (requirements
// doc item 20) — exact sibling of chiller_transfers, one entry per blast
// freezer involved in this Slaughter ID.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->json('blast_freezer_transfers')->nullable()->after('chiller_transfers');
        });
    }

    public function down(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->dropColumn('blast_freezer_transfers');
        });
    }
};
