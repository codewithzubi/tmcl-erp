<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE shipments MODIFY shipment_method ENUM('Air', 'Sea', 'Road') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE shipments MODIFY shipment_method ENUM('Air', 'Sea') NOT NULL");
    }
};
