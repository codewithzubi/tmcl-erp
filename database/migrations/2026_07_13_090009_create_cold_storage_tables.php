<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_code')->unique();
            $table->string('name');
            $table->enum('type', ['Chiller', 'Blast Freezer', 'Freezer']);
            $table->decimal('capacity_kg', 12, 2);
            $table->decimal('occupied_kg', 12, 2)->default(0);
            $table->decimal('min_temp', 5, 2);
            $table->decimal('max_temp', 5, 2);
            $table->decimal('target_temp', 5, 2);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });

        Schema::create('storage_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_number')->unique();
            $table->foreignId('storage_unit_id')->constrained('storage_units')->restrictOnDelete();
            $table->foreignId('lot_id')->constrained('lots')->restrictOnDelete();
            $table->decimal('product_weight', 12, 2);
            $table->dateTime('time_in');
            $table->dateTime('time_out')->nullable();
            $table->enum('status', ['Active', 'Completed'])->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_sessions');
        Schema::dropIfExists('storage_units');
    }
};
