<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('sales_order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->string('customer_name');
            $table->enum('shipment_method', ['Air', 'Sea']);
            $table->date('shipment_date');
            $table->date('expected_delivery_date');
            $table->string('destination_country');
            $table->string('destination_port');
            $table->string('shipping_line')->nullable();
            $table->string('vessel_or_flight_number')->nullable();
            $table->string('container_or_awb_number')->nullable();
            $table->string('freight_forwarder')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->enum('shipment_status', ['Planned', 'In Transit', 'Delivered'])->default('Planned');
            $table->timestamps();
        });

        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->string('dispatch_number')->unique();
            $table->foreignId('shipment_id')->constrained('shipments')->restrictOnDelete();
            $table->foreignId('lot_id')->constrained('lots')->restrictOnDelete();
            $table->foreignId('carton_id')->constrained('cartons')->restrictOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->decimal('total_weight_kg', 10, 2);
            $table->dateTime('dispatch_time');
            $table->string('dispatch_officer');
            $table->enum('status', ['Pending', 'Dispatched'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatches');
        Schema::dropIfExists('shipments');
    }
};
