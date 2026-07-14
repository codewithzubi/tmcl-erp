<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grns', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number')->unique();
            $table->foreignId('supplier_purchase_order_id')->nullable()->constrained('supplier_purchase_orders')->nullOnDelete();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('gate_entry_id')->constrained()->restrictOnDelete();
            $table->date('receipt_date');
            $table->unsignedInteger('number_of_animals_received');
            $table->decimal('total_weight_received', 12, 2);
            $table->string('receiving_officer');
            $table->enum('inspection_status', ['Pending', 'Passed', 'Failed', 'Partial'])->default('Pending');
            $table->unsignedInteger('accepted_animals')->default(0);
            $table->unsignedInteger('rejected_animals')->default(0);
            $table->decimal('accepted_weight', 12, 2)->default(0);
            $table->decimal('rejected_weight', 12, 2)->default(0);
            $table->text('rejection_reason')->nullable();
            $table->text('veterinary_remarks')->nullable();
            $table->enum('status', ['Draft', 'Completed', 'Cancelled'])->default('Draft');
            $table->timestamps();
        });

        Schema::create('livestock_inspections', function (Blueprint $table) {
            $table->id();
            $table->string('inspection_number')->unique();
            $table->foreignId('grn_id')->constrained('grns')->cascadeOnDelete();
            $table->string('veterinary_officer');
            $table->date('inspection_date');
            $table->enum('animal_health_status', ['Healthy', 'Sick', 'Injured', 'Under Observation']);
            $table->text('disease_symptoms')->nullable();
            $table->enum('physical_condition', ['Good', 'Fair', 'Poor']);
            $table->decimal('body_weight_verification', 12, 2);
            $table->decimal('temperature', 5, 2);
            $table->boolean('quarantine_required')->default(false);
            $table->text('inspection_remarks')->nullable();
            $table->enum('final_decision', ['Accept', 'Reject']);
            $table->timestamps();
        });

        Schema::create('barn_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('allocation_number')->unique();
            $table->foreignId('grn_id')->constrained('grns')->cascadeOnDelete();
            $table->string('barn');
            $table->string('batch_number')->unique();
            $table->string('livestock_type');
            $table->unsignedInteger('number_of_animals_allocated');
            $table->decimal('total_weight', 12, 2);
            $table->date('allocation_date');
            $table->string('supervisor');
            $table->text('remarks')->nullable();
            $table->enum('allocation_status', ['Allocated', 'Moved', 'Completed'])->default('Allocated');
            $table->timestamps();
        });

        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->string('lot_code')->unique();
            $table->string('lot_name')->nullable();
            $table->foreignId('sales_order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('supplier_purchase_order_id')->nullable()->constrained('supplier_purchase_orders')->nullOnDelete();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('grn_id')->constrained('grns')->restrictOnDelete();
            $table->foreignId('barn_allocation_id')->nullable()->constrained('barn_allocations')->nullOnDelete();
            $table->string('batch_number')->nullable();
            $table->string('livestock_type');
            $table->unsignedInteger('number_of_animals');
            $table->decimal('total_live_weight', 12, 2);
            $table->date('allocation_date');
            $table->string('created_by');
            $table->enum('status', ['Open', 'Hold', 'Completed'])->default('Open');
            $table->text('remarks')->nullable();
            $table->decimal('supplier_committed_weight', 12, 2);
            $table->text('hold_reason')->nullable();
            $table->decimal('required_remaining_weight', 12, 2)->nullable();
            $table->unsignedInteger('additional_animals_required')->nullable();
            $table->string('released_by')->nullable();
            $table->date('release_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots');
        Schema::dropIfExists('barn_allocations');
        Schema::dropIfExists('livestock_inspections');
        Schema::dropIfExists('grns');
    }
};
