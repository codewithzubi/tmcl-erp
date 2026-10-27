<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Exporting straight from Container Management — remembers which container an
// export came from, so each container knows how much of it is already exported.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('export_entries', function (Blueprint $table) {
            $table->foreignId('container_id')->nullable()->after('tag_allocations')->constrained('containers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('export_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('container_id');
        });
    }
};
