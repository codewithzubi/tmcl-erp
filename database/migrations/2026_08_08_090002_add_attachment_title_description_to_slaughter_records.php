<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->string('attachment_title')->nullable()->after('attachment_path');
            $table->text('attachment_description')->nullable()->after('attachment_title');
        });
    }

    public function down(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->dropColumn(['attachment_title', 'attachment_description']);
        });
    }
};
