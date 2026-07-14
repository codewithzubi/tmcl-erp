<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slaughter_records', function (Blueprint $table) {
            $table->id();
            $table->string('animal_code')->unique();
            $table->foreignId('lot_id')->constrained('lots')->restrictOnDelete();
            $table->string('sales_order_number')->nullable();
            $table->unsignedInteger('animal_sequence_number');
            $table->date('slaughter_date');
            $table->string('slaughter_operator');
            $table->enum('processing_status', ['In Progress', 'Completed'])->default('In Progress');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('offal_recoveries', function (Blueprint $table) {
            $table->id();
            $table->string('recovery_number')->unique();
            $table->foreignId('slaughter_record_id')->constrained('slaughter_records')->cascadeOnDelete();
            $table->date('recovery_date');
            $table->enum('recovery_type', ['Offal', 'Fat', 'Hide/Skin', 'Waste', 'Other']);
            $table->decimal('measured_weight', 10, 2);
            $table->string('recorded_by');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('carcass_weight_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slaughter_record_id')->constrained('slaughter_records')->cascadeOnDelete();
            $table->decimal('hanging_weight', 10, 2);
            $table->dateTime('weight_date_time');
            $table->string('scale_id');
            $table->decimal('left_hind_quarter', 10, 2);
            $table->decimal('right_hind_quarter', 10, 2);
            $table->decimal('left_fore_quarter', 10, 2);
            $table->decimal('right_fore_quarter', 10, 2);
            $table->boolean('manual_override')->default(false);
            $table->enum('supervisor_approval', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->decimal('final_carcass_weight', 10, 2);
            $table->timestamps();
        });

        Schema::create('veterinary_inspections', function (Blueprint $table) {
            $table->id();
            $table->string('inspection_number')->unique();
            $table->foreignId('slaughter_record_id')->constrained('slaughter_records')->cascadeOnDelete();
            $table->string('doctor');
            $table->date('inspection_date');
            $table->enum('inspection_result', ['Approved', 'Partial Reject', 'Full Reject']);
            $table->text('disease_observation')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('meat_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slaughter_record_id')->constrained('slaughter_records')->cascadeOnDelete();
            $table->enum('deduction_type', ['Partial', 'Full']);
            $table->enum('rejected_portion', ['Fore Quarter', 'Hind Quarter', 'Full Carcass', 'Other']);
            $table->decimal('rejected_weight', 10, 2);
            $table->string('reason');
            $table->text('remarks')->nullable();
            $table->string('approved_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meat_deductions');
        Schema::dropIfExists('veterinary_inspections');
        Schema::dropIfExists('carcass_weight_records');
        Schema::dropIfExists('offal_recoveries');
        Schema::dropIfExists('slaughter_records');
    }
};
