<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// End Slaughter's Rejected Pieces + Re-Weighting sections (tomcl ops scope
// doc, pages 11-12): which piece tag IDs were rejected, and the optional
// trim/re-weigh record kept per rejected piece.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->json('rejected_piece_ids')->nullable()->after('custom_adjustments');
            $table->json('re_weight_entries')->nullable()->after('rejected_piece_ids');
        });
    }

    public function down(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->dropColumn(['rejected_piece_ids', 're_weight_entries']);
        });
    }
};
