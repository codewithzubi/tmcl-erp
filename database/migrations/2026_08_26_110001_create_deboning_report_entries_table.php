<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Reports > Deboning Report — one row per confirmed Boneless Processing
// record, mirroring the floor's Excel "DEBONING REPORT" sheet. Hot Weight,
// Boneless Weight, Bone Weight, Debone Date, Animal Type/Customer/
// Destination are all resolved live from the linked Boneless Processing/
// Slaughter record. The per-cut breakdown (Striploin, Tenderloin, T-Bone,
// and any ad-hoc/party-specific cut) is a free-form list rather than fixed
// columns — cut_breakdown holds [{name, cartons, net_weight}, ...] so a new
// cut type never needs a schema change.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deboning_report_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boneless_processing_record_id')->constrained()->cascadeOnDelete();
            $table->date('production_date')->nullable();
            $table->string('description')->nullable();
            $table->decimal('no_of_animals', 10, 2)->nullable();
            $table->json('cut_breakdown')->nullable();
            $table->decimal('new_balance_boneless', 10, 2)->nullable();
            $table->decimal('old_balance_boneless_used', 10, 2)->nullable();
            $table->decimal('send_for_other_party', 10, 2)->nullable();
            $table->decimal('trimming', 10, 2)->nullable();
            $table->decimal('rejected_flank', 10, 2)->nullable();
            $table->decimal('rejected_meat', 10, 2)->nullable();
            $table->decimal('wastage', 10, 2)->nullable();
            $table->decimal('tendon', 10, 2)->nullable();
            $table->decimal('boneless_boti', 10, 2)->nullable();
            $table->decimal('kitchen_issued', 10, 2)->nullable();
            $table->decimal('bone_issued', 10, 2)->nullable();
            $table->decimal('nalli_issued', 10, 2)->nullable();
            $table->decimal('fat_issued', 10, 2)->nullable();
            $table->string('irani_dr_vet_code')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deboning_report_entries');
    }
};
