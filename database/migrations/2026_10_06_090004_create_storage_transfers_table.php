<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slaughter_record_id')->constrained('slaughter_records')->cascadeOnDelete();
            $table->string('from_type');
            $table->string('from_name');
            $table->string('to_type');
            $table->string('to_name');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->json('tag_ids')->nullable();
            $table->decimal('weight', 10, 2)->default(0);
            $table->text('comments')->nullable();
            // Optional — only filled when the source chiller/blast freezer is
            // being shut down (breakdown) as part of this transfer.
            $table->timestamp('close_time')->nullable();
            $table->string('transferred_by')->nullable();
            $table->timestamp('transferred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_transfers');
    }
};
