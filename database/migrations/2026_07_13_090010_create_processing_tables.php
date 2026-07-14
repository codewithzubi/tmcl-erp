<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boneless_processing_records', function (Blueprint $table) {
            $table->id();
            $table->string('processing_number')->unique();
            $table->foreignId('lot_id')->constrained('lots')->restrictOnDelete();
            $table->foreignId('slaughter_record_id')->constrained('slaughter_records')->restrictOnDelete();
            $table->date('processing_date');
            $table->decimal('input_weight', 10, 2);
            $table->decimal('boneless_weight', 10, 2);
            $table->decimal('bone_weight', 10, 2);
            $table->string('operator');
            $table->enum('status', ['In Progress', 'Completed'])->default('In Progress');
            $table->timestamps();
        });

        Schema::create('boti_processing_records', function (Blueprint $table) {
            $table->id();
            $table->string('processing_number')->unique();
            $table->foreignId('lot_id')->constrained('lots')->restrictOnDelete();
            $table->foreignId('slaughter_record_id')->constrained('slaughter_records')->restrictOnDelete();
            $table->date('processing_date');
            $table->decimal('input_weight', 10, 2);
            $table->decimal('boti_weight', 10, 2);
            $table->decimal('bone_weight', 10, 2);
            $table->string('operator');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boti_processing_records');
        Schema::dropIfExists('boneless_processing_records');
    }
};
