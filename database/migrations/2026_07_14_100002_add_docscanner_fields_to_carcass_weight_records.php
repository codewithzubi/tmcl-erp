<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Additive fields from the DocScanner "Carcass Weight" batch-entry
    // wireframe — animal descriptors, Hook Weight, per-row Lock, and a photo
    // reference. Nothing existing is removed.
    public function up(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('slaughter_record_id');
            $table->string('specie')->nullable()->after('gender');
            $table->string('age')->nullable()->after('specie');
            $table->string('teeth')->nullable()->after('age');
            $table->decimal('hook_weight', 10, 2)->nullable()->after('teeth');
            $table->string('photo_path')->nullable()->after('final_carcass_weight');
            $table->boolean('locked')->default(false)->after('photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('carcass_weight_records', function (Blueprint $table) {
            $table->dropColumn(['gender', 'specie', 'age', 'teeth', 'hook_weight', 'photo_path', 'locked']);
        });
    }
};
