<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_settlements', function (Blueprint $table) {
            $table->id();
            $table->string('settlement_number')->unique();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('lot_id')->constrained('lots')->restrictOnDelete();
            $table->decimal('agreed_rate_per_kg', 10, 2);
            $table->decimal('approved_meat_weight', 12, 2);
            $table->decimal('total_settlement_amount', 14, 2);
            $table->enum('payment_method', ['Bank Transfer', 'Cash', 'Cheque']);
            $table->date('payment_date')->nullable();
            $table->enum('settlement_status', ['Pending', 'Approved', 'Paid'])->default('Pending');
            $table->string('approved_by');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('offal_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('lot_id')->constrained('lots')->restrictOnDelete();
            $table->string('by_product_type');
            $table->decimal('total_weight', 12, 2);
            $table->enum('disposal_method', ['Return to Supplier', 'Purchase by Company']);
            $table->decimal('purchase_rate', 10, 2)->nullable();
            $table->decimal('purchase_amount', 14, 2)->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Paid'])->default('Pending');
            $table->timestamps();
        });

        Schema::create('meat_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('allocation_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('sales_order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->foreignId('lot_id')->constrained('lots')->restrictOnDelete();
            $table->enum('product_type', ['Full Carcass', 'Boneless', 'Boti']);
            $table->decimal('quantity', 12, 2);
            $table->enum('destination_department', ['Cold Storage', 'Boneless Department', 'Boti Department']);
            $table->date('allocation_date');
            $table->enum('status', ['Pending', 'Routed', 'Completed'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meat_allocations');
        Schema::dropIfExists('offal_settlements');
        Schema::dropIfExists('supplier_settlements');
    }
};
