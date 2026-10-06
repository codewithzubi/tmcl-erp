<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->timestamp('reopened_at')->nullable()->after('end_slaughter_at');
            $table->string('reopened_by')->nullable()->after('reopened_at');
        });
    }

    public function down(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->dropColumn(['reopened_at', 'reopened_by']);
        });
    }
};
