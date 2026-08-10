<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// event_logs.new_value stores a JSON snapshot of the updated fields for the
// "Field History" audit trail. Several tracked records (Slaughter, Carcass
// Weight) can carry a base64 attachment/photo well over the 65KB TEXT limit,
// which made the whole update request fail with a 500 the moment the audit
// log insert overflowed the column — long after the actual record update had
// already succeeded. Widened to LONGTEXT (4GB) so no future field size can
// crash the request this way. Same raw-SQL pattern as
// 2026_08_05_090003_widen_attachment_path_on_slaughter_records.php.
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE event_logs MODIFY new_value LONGTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE event_logs MODIFY new_value TEXT NULL');
    }
};
