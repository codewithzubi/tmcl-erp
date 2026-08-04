<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_attachments', function (Blueprint $table) {
            $table->text('description')->nullable()->after('file_type');
            // Base64 data URL — the actual file content, so View/Download work
            // for real instead of just showing filename metadata.
            $table->longText('file_data')->nullable()->after('size_kb');
        });
    }

    public function down(): void
    {
        Schema::table('supplier_attachments', function (Blueprint $table) {
            $table->dropColumn(['description', 'file_data']);
        });
    }
};
