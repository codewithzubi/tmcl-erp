<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('slaughter_record_id')->constrained()->nullOnDelete();
            $table->string('chiller_name')->nullable()->after('customer_id');
            $table->string('print_tags')->nullable()->after('chiller_name');
            $table->string('cut_type')->nullable()->after('print_tags');
        });
    }

    public function down(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
            $table->dropColumn(['chiller_name', 'print_tags', 'cut_type']);
        });
    }
};
