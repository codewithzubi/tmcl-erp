<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// One row per Slaughter ID for the "Slaughtering Report" screen (Reports
// section). Everything the app already tracks for that Slaughter ID
// (Supplier, Customer, Destination, Specie, Total Animals, Rejected
// Pieces/Weight, Final Weight) is resolved live from the linked
// slaughter_records/carcass_weight_records on the frontend, not duplicated
// here — this table only holds the handful of fields that have no existing
// home elsewhere in the app.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slaughtering_report_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slaughter_record_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('dual_pcs')->default(0);
            $table->unsignedInteger('quarter_pcs')->default(0);
            $table->decimal('total_gross_weight', 10, 2);
            $table->decimal('live_weight', 10, 2)->nullable();
            $table->string('freight')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slaughtering_report_entries');
    }
};
