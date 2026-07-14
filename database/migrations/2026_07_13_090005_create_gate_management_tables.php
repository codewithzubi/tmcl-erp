<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gate_entries', function (Blueprint $table) {
            $table->id();
            $table->string('gate_entry_number')->unique();
            $table->dateTime('entry_date_time');
            $table->enum('entry_type', ['Supplier Delivery', 'Visitor', 'Vehicle Return', 'Other']);
            $table->string('visitor_name')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('driver_name');
            $table->string('driver_cnic');
            $table->string('vehicle_registration_number');
            $table->enum('vehicle_type', ['Truck', 'Container Truck', 'Pickup', 'Trailer', 'Other']);
            $table->string('trailer_number')->nullable();
            $table->unsignedInteger('number_of_animals')->nullable();
            $table->decimal('estimated_weight', 12, 2)->nullable();
            $table->string('security_officer');
            $table->enum('purpose_of_visit', ['Livestock Delivery', 'Inspection', 'Meeting', 'Maintenance', 'Other']);
            $table->text('remarks')->nullable();
            $table->enum('approval_status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->timestamps();
        });

        Schema::create('gate_entry_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gate_entry_id')->constrained()->cascadeOnDelete();
            $table->enum('slot', ['entry_photograph', 'driver_cnic_copy', 'vehicle_documents', 'additional']);
            $table->string('file_name');
            $table->string('file_type');
            $table->unsignedInteger('size_kb');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gate_entry_attachments');
        Schema::dropIfExists('gate_entries');
    }
};
