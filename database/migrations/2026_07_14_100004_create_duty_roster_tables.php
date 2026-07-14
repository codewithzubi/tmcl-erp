<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // New module from the DocScanner wireframes: Shift definitions and a
    // per-day duty roster assigning staff to a shift.
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('shift_name');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });

        Schema::create('duty_rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->date('duty_date');
            $table->string('department')->nullable();
            $table->enum('status', ['Scheduled', 'Completed', 'Absent'])->default('Scheduled');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'duty_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duty_rosters');
        Schema::dropIfExists('shifts');
    }
};
