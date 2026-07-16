<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Remaining fields from the "tomcl ops" doc's New Slaughter table (page 2)
    // that weren't covered by the earlier DocScanner migration: Start Date/time
    // (with time precision), Teeth, Age, Gender, Specie, and Attachment.
    public function up(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->dateTime('start_datetime')->nullable()->after('slaughter_operator');
            $table->string('teeth')->nullable()->after('carcass_type');
            $table->string('age')->nullable()->after('teeth');
            $table->string('gender')->nullable()->after('age');
            $table->string('specie')->nullable()->after('gender');
            $table->string('attachment_path')->nullable()->after('specie');
        });
    }

    public function down(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->dropColumn(['start_datetime', 'teeth', 'age', 'gender', 'specie', 'attachment_path']);
        });
    }
};
