<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Reports > Loading Report — one row per confirmed export/loading allocation
// (export_entries), mirroring the floor's Excel "LOADING REPORT" sheet.
// Shipment date/time, freight mode, destination, consignee, customer,
// reference, remarks and loading weight are all resolved live from the
// linked Export Entry — only the columns with no existing home elsewhere in
// the app (loading/offload paperwork detail) are stored here.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loading_report_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('export_entry_id')->constrained()->cascadeOnDelete();
            $table->string('description')->nullable();
            $table->unsignedInteger('total_pcs')->nullable();
            $table->decimal('hot_weight', 10, 2)->nullable();
            $table->unsignedInteger('basket_crtn')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('container_no')->nullable();
            $table->string('seal_no')->nullable();
            $table->string('gate_pass_no')->nullable();
            $table->dateTime('chilling_start_time')->nullable();
            $table->dateTime('chilling_end_time')->nullable();
            $table->string('indent_no')->nullable();
            $table->dateTime('offload_date_time')->nullable();
            $table->unsignedInteger('offload_total_pcs')->nullable();
            $table->decimal('offload_total_weight', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loading_report_entries');
    }
};
