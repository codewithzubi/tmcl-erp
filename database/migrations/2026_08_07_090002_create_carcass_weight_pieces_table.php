<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carcass_weight_pieces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carcass_weight_record_id')->constrained()->cascadeOnDelete();
            $table->string('piece_name');
            $table->unsignedInteger('serial_no');
            $table->string('composite_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carcass_weight_pieces');
    }
};
