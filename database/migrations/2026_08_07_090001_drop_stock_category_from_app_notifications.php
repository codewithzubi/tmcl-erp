<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// The "Stock" notification category was never populated by anything and had
// no use — dropped down to the two categories actually in use. Raw SQL
// (rather than Schema::table(...)->change()) since doctrine/dbal isn't
// installed in this project.
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE app_notifications SET category = 'Alert' WHERE category = 'Stock'");
        DB::statement("ALTER TABLE app_notifications MODIFY category ENUM('Event', 'Alert') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE app_notifications MODIFY category ENUM('Event', 'Stock', 'Alert') NOT NULL");
    }
};
