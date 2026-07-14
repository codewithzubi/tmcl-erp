<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packets', function (Blueprint $table) {
            $table->id();
            $table->string('packet_number')->unique();
            $table->foreignId('lot_id')->constrained('lots')->restrictOnDelete();
            $table->enum('product_type', ['Full Carcass', 'Boneless', 'Boti']);
            $table->decimal('packet_size_kg', 8, 2);
            $table->unsignedInteger('number_of_packets');
            $table->decimal('weight_per_packet_kg', 8, 2);
            $table->string('packaging_material');
            $table->string('packed_by');
            $table->date('packing_date');
            $table->timestamps();
        });

        Schema::create('cartons', function (Blueprint $table) {
            $table->id();
            $table->string('carton_number')->unique();
            $table->foreignId('lot_id')->constrained('lots')->restrictOnDelete();
            $table->unsignedInteger('number_of_packets');
            $table->decimal('carton_weight_kg', 10, 2);
            $table->string('packaging_material');
            $table->string('barcode')->unique();
            $table->boolean('label_printed')->default(false);
            $table->enum('status', ['Open', 'Sealed', 'Dispatched'])->default('Open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cartons');
        Schema::dropIfExists('packets');
    }
};
