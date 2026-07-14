<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // New, additive mechanism from the DocScanner wireframes: lets an admin
    // attach ad-hoc fields to any existing screen/module without touching
    // that module's own table.
    public function up(): void
    {
        Schema::create('custom_field_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->string('field_key');
            $table->string('label');
            $table->enum('field_type', ['text', 'number', 'date', 'select', 'checkbox', 'textarea'])->default('text');
            $table->json('options')->nullable();
            $table->boolean('required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();

            $table->unique(['module', 'field_key']);
        });

        Schema::create('custom_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_field_definition_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('record_id');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['custom_field_definition_id', 'record_id'], 'custom_field_values_definition_record_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_field_values');
        Schema::dropIfExists('custom_field_definitions');
    }
};
