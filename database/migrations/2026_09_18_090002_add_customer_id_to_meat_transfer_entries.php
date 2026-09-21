<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Requirements doc item 18 — Internal Transfer (further processing) had no
// customer field at all; Export's already-existing customer_buyer stays a
// free-text name (unchanged), but Internal Transfer gets a real FK since
// it's a brand-new field with no prior string-based consumers to match.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meat_transfer_entries', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('transfer_department')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('meat_transfer_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
        });
    }
};
