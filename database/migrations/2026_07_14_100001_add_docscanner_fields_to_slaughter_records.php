<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Additive fields from the DocScanner "New Slaughter" wireframe — layered
    // on top of the existing Scope Doc slaughter_records table, nothing removed.
    public function up(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('lot_id')->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->after('supplier_id')->constrained()->nullOnDelete();
            $table->string('agent')->nullable()->after('customer_id');
            $table->string('doctor')->nullable()->after('agent');
            $table->string('meat_checker')->nullable()->after('doctor');
            $table->string('destination')->nullable()->after('meat_checker');
            $table->string('final_product')->nullable()->after('destination');
            $table->foreignId('planned_chiller_id')->nullable()->after('final_product')->constrained('storage_units')->nullOnDelete();
            $table->string('belt_attachment')->nullable()->after('planned_chiller_id');
            $table->string('carcass_type')->nullable()->after('belt_attachment');
            $table->timestamp('end_slaughter_at')->nullable()->after('carcass_type');
            $table->decimal('rejection_weight', 10, 2)->nullable()->after('end_slaughter_at');
            $table->decimal('final_weight', 10, 2)->nullable()->after('rejection_weight');
        });
    }

    public function down(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
            $table->dropConstrainedForeignId('customer_id');
            $table->dropConstrainedForeignId('planned_chiller_id');
            $table->dropColumn([
                'agent', 'doctor', 'meat_checker', 'destination', 'final_product',
                'belt_attachment', 'carcass_type', 'end_slaughter_at', 'rejection_weight', 'final_weight',
            ]);
        });
    }
};
