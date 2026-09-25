<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('export_entries', function (Blueprint $table) {
            $table->json('extra_materials')->nullable()->after('mode_details');
        });
    }

    public function down(): void
    {
        Schema::table('export_entries', function (Blueprint $table) {
            $table->dropColumn('extra_materials');
        });
    }
};
