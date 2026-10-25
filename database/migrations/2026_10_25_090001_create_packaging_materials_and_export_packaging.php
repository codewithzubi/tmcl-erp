<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// 20 Oct feedback, points 6–7 — Packaging setup (Dry Ice, Thermopole Box...)
// issued per export like Extra Material, and the "Road" export mode renamed
// to "Land".
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packaging_materials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('cost', 12, 2);
            $table->timestamps();
        });

        Schema::table('export_entries', function (Blueprint $table) {
            $table->json('packaging')->nullable()->after('extra_materials');
        });

        DB::table('export_entries')->where('export_mode', 'Road')->update(['export_mode' => 'Land']);

        DB::statement("ALTER TABLE shipments MODIFY shipment_method ENUM('Air', 'Sea', 'Road', 'Land') NOT NULL");
        DB::table('shipments')->where('shipment_method', 'Road')->update(['shipment_method' => 'Land']);
        DB::statement("ALTER TABLE shipments MODIFY shipment_method ENUM('Air', 'Sea', 'Land') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE shipments MODIFY shipment_method ENUM('Air', 'Sea', 'Road', 'Land') NOT NULL");
        DB::table('shipments')->where('shipment_method', 'Land')->update(['shipment_method' => 'Road']);
        DB::statement("ALTER TABLE shipments MODIFY shipment_method ENUM('Air', 'Sea', 'Road') NOT NULL");

        DB::table('export_entries')->where('export_mode', 'Land')->update(['export_mode' => 'Road']);

        Schema::table('export_entries', function (Blueprint $table) {
            $table->dropColumn('packaging');
        });

        Schema::dropIfExists('packaging_materials');
    }
};
