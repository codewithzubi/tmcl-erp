<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// attachment_path was a plain filename string with no real file storage.
// Widened to LONGTEXT so it can hold a base64 data URL instead, the same
// pattern used for users.picture and supplier_attachments.file_data.
// Raw SQL is used here instead of Schema::table()->change() to avoid
// pulling in doctrine/dbal just for a single column-type change.
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE slaughter_records MODIFY attachment_path LONGTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE slaughter_records MODIFY attachment_path VARCHAR(255) NULL');
    }
};
