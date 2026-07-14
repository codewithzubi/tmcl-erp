<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_code')->unique();
            $table->enum('supplier_type', ['Individual Farmer', 'Livestock Trader', 'Feedlot/Farm Company']);
            $table->string('company_name');
            $table->string('supplier_name');
            $table->string('contact_person');
            $table->string('cnic_or_registration_no');
            $table->string('mobile');
            $table->string('email');
            $table->text('address');
            $table->string('city');
            $table->string('country');
            $table->string('tax_registration_number')->nullable();
            $table->string('payment_terms');
            $table->text('bank_details')->nullable();
            $table->string('currency');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('supplier_contact_persons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('designation');
            $table->string('email');
            $table->string('mobile');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('supplier_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_type');
            $table->string('uploaded_by');
            $table->unsignedInteger('size_kb');
            $table->timestamps();
        });

        Schema::create('livestock_supply_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('grn_number');
            $table->string('livestock_type');
            $table->unsignedInteger('number_of_animals');
            $table->decimal('total_weight_kg', 12, 2);
            $table->date('receipt_date');
            $table->enum('status', ['Accepted', 'Partially Accepted', 'Rejected']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_supply_records');
        Schema::dropIfExists('supplier_attachments');
        Schema::dropIfExists('supplier_contact_persons');
        Schema::dropIfExists('suppliers');
    }
};
