<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meat_transfer_entries', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('transfer_department')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('meat_transfer_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
        });
    }
};
