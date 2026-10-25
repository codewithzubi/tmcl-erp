<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 20 Oct feedback, point 12 — Container Management. Containers are generated
// in series named Container-<Vendor>-<Date>-<Serial>; Boti/Boneless meat
// transfers record which container their cartons went into.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('vendor');
            $table->date('container_date');
            $table->unsignedInteger('serial');
            $table->enum('status', ['Open', 'Closed'])->default('Open');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['vendor', 'container_date', 'serial']);
        });

        Schema::table('meat_transfer_entries', function (Blueprint $table) {
            $table->foreignId('container_id')->nullable()->after('yield_percent')->constrained('containers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('meat_transfer_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('container_id');
        });

        Schema::dropIfExists('containers');
    }
};
