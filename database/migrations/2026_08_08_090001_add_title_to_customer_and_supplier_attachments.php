<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_attachments', function (Blueprint $table) {
            $table->string('title')->nullable()->after('customer_id');
        });
        Schema::table('supplier_attachments', function (Blueprint $table) {
            $table->string('title')->nullable()->after('supplier_id');
        });
    }

    public function down(): void
    {
        Schema::table('customer_attachments', function (Blueprint $table) {
            $table->dropColumn('title');
        });
        Schema::table('supplier_attachments', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
};
