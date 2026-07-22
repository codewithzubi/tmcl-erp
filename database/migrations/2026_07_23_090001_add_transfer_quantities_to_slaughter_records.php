<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->decimal('chiller_transfer_qty', 10, 2)->nullable()->after('final_weight');
            $table->decimal('blast_freezer_transfer_qty', 10, 2)->nullable()->after('chiller_transfer_qty');
            $table->decimal('boti_transfer_qty', 10, 2)->nullable()->after('blast_freezer_transfer_qty');
            $table->decimal('boneless_transfer_qty', 10, 2)->nullable()->after('boti_transfer_qty');
        });
    }

    public function down(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->dropColumn([
                'chiller_transfer_qty',
                'blast_freezer_transfer_qty',
                'boti_transfer_qty',
                'boneless_transfer_qty',
            ]);
        });
    }
};
