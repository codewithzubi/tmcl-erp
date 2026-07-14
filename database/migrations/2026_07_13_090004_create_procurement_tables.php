<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('pr_number')->unique();
            $table->date('pr_date');
            $table->foreignId('linked_sales_order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->string('requesting_department');
            $table->string('procurement_officer');
            $table->string('livestock_type');
            $table->unsignedInteger('required_quantity');
            $table->decimal('estimated_weight_kg', 12, 2);
            $table->date('expected_delivery_date');
            $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent'])->default('Medium');
            $table->text('remarks')->nullable();
            $table->enum('status', ['Pending Approval', 'Approved', 'Rejected', 'Closed'])->default('Pending Approval');
            $table->timestamps();
        });

        Schema::create('supplier_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->foreignId('purchase_requisition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->date('quotation_date');
            $table->decimal('price_per_kg', 10, 2);
            $table->unsignedInteger('number_of_animals');
            $table->decimal('total_weight_kg', 12, 2);
            $table->decimal('delivery_charges', 12, 2)->default(0);
            $table->string('payment_terms');
            $table->date('delivery_schedule');
            $table->enum('status', ['Received', 'Under Review', 'Selected', 'Rejected'])->default('Received');
            $table->timestamps();
        });

        Schema::create('supplier_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_order_number')->unique();
            $table->foreignId('purchase_requisition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('quotation_id')->constrained('supplier_quotations')->restrictOnDelete();
            $table->date('po_date');
            $table->date('delivery_date');
            $table->string('livestock_type');
            $table->unsignedInteger('quantity');
            $table->decimal('estimated_weight_kg', 12, 2);
            $table->decimal('unit_rate', 10, 2);
            $table->decimal('total_amount', 14, 2);
            $table->text('terms_and_conditions')->nullable();
            $table->enum('supplier_approval_status', ['Pending', 'Accepted', 'Declined'])->default('Pending');
            $table->enum('purchase_order_status', ['Draft', 'Sent to Supplier', 'Confirmed', 'Completed', 'Cancelled'])->default('Draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_purchase_orders');
        Schema::dropIfExists('supplier_quotations');
        Schema::dropIfExists('purchase_requisitions');
    }
};
