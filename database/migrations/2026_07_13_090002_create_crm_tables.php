<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code')->unique();
            $table->enum('customer_type', ['Local', 'International']);
            $table->string('company_name');
            $table->string('customer_name');
            $table->string('contact_person');
            $table->string('designation')->nullable();
            $table->string('email');
            $table->string('mobile');
            $table->string('landline')->nullable();
            $table->string('website')->nullable();
            $table->string('industry_type');
            $table->string('customer_category');
            $table->string('tax_registration_number')->nullable();
            $table->string('currency');
            $table->string('payment_terms');
            $table->text('billing_address');
            $table->text('shipping_address');
            $table->string('country');
            $table->string('city');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_contact_persons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('designation');
            $table->string('email');
            $table->string('mobile');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('customer_discussion_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('author');
            $table->text('note');
            $table->timestamps();
        });

        Schema::create('customer_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_type');
            $table->string('uploaded_by');
            $table->unsignedInteger('size_kb');
            $table->timestamps();
        });

        Schema::create('requirements', function (Blueprint $table) {
            $table->id();
            $table->string('requirement_code')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('product_type');
            $table->text('product_specifications');
            $table->decimal('quantity', 12, 2);
            $table->string('unit_of_measure');
            $table->string('packaging_requirement');
            $table->string('delivery_location');
            $table->date('expected_delivery_date');
            $table->text('additional_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->string('proposal_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->date('proposal_date');
            $table->date('valid_until');
            $table->string('currency');
            $table->enum('status', ['Draft', 'Sent', 'Accepted', 'Rejected', 'Expired'])->default('Draft');
            $table->unsignedInteger('version_number')->default(1);
            $table->string('prepared_by');
            $table->text('internal_remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('proposal_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->string('product');
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 2);
            $table->string('unit');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('discount_pct', 5, 2)->default(0);
            $table->decimal('tax_pct', 5, 2)->default(0);
            $table->decimal('packaging_charges', 12, 2)->default(0);
            $table->decimal('logistics_charges', 12, 2)->default(0);
            $table->decimal('freight_charges', 12, 2)->default(0);
            $table->decimal('insurance_charges', 12, 2)->default(0);
            $table->decimal('other_charges', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('customer_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_order_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('linked_proposal_id')->nullable()->constrained('proposals')->nullOnDelete();
            $table->date('po_date');
            $table->date('delivery_date');
            $table->enum('status', ['Pending Review', 'Approved', 'Rejected'])->default('Pending Review');
            $table->text('internal_remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('sales_order_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('linked_proposal_id')->nullable()->constrained('proposals')->nullOnDelete();
            $table->foreignId('linked_purchase_order_id')->nullable()->constrained('customer_purchase_orders')->nullOnDelete();
            $table->date('order_date');
            $table->decimal('order_value', 14, 2);
            $table->enum('approval_status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->enum('production_status', ['Not Started', 'In Progress', 'Completed'])->default('Not Started');
            $table->string('logistics_status');
            $table->enum('invoice_status', ['Not Invoiced', 'Partially Invoiced', 'Invoiced'])->default('Not Invoiced');
            $table->enum('payment_status', ['Unpaid', 'Partially Paid', 'Paid'])->default('Unpaid');
            $table->enum('overall_status', ['Open', 'In Progress', 'Completed', 'Cancelled'])->default('Open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
        Schema::dropIfExists('customer_purchase_orders');
        Schema::dropIfExists('proposal_line_items');
        Schema::dropIfExists('proposals');
        Schema::dropIfExists('requirements');
        Schema::dropIfExists('customer_attachments');
        Schema::dropIfExists('customer_discussion_notes');
        Schema::dropIfExists('customer_contact_persons');
        Schema::dropIfExists('customers');
    }
};
