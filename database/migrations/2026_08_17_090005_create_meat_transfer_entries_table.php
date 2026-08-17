<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Meat Transfer/Loading/Export scope doc, Section A: one row per confirmed
// movement of meat out of a chiller — to an internal department for now
// (transaction_type "Internal Transfer"); Export/Shipment types are not
// implemented yet but the column already allows for them later.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meat_transfer_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slaughter_record_id')->constrained()->cascadeOnDelete();
            $table->string('chiller_name');
            $table->dateTime('chiller_out_time');
            $table->string('transaction_type');
            $table->string('transfer_department')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meat_transfer_entries');
    }
};
