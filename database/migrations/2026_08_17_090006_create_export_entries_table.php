<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Meat Transfer/Loading/Export scope doc, Section B: one row per confirmed
// export/shipment allocation out of a chiller. mode_details holds the
// fields specific to whichever Export Mode was picked (Air/Sea/Road) so
// this table doesn't need ~25 mostly-null columns for the three shapes.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('export_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slaughter_record_id')->constrained()->cascadeOnDelete();
            $table->string('chiller_name');
            $table->dateTime('chiller_out_time');
            $table->dateTime('export_date_time');
            $table->decimal('export_quantity', 10, 2);
            $table->string('destination_country')->nullable();
            $table->string('destination_consignee')->nullable();
            $table->string('customer_buyer')->nullable();
            $table->string('forwarder_name')->nullable();
            $table->string('export_reference')->nullable();
            $table->text('remarks')->nullable();
            $table->string('export_mode');
            $table->json('mode_details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_entries');
    }
};
