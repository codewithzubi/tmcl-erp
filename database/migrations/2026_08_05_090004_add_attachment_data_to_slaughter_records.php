<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// attachment_path stays the display filename; attachment_data holds the
// actual file content as a base64 data URL, attachment_type its MIME type
// — same pattern as users.picture / supplier_attachments.file_data.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->string('attachment_type')->nullable()->after('attachment_path');
            $table->longText('attachment_data')->nullable()->after('attachment_type');
        });
    }

    public function down(): void
    {
        Schema::table('slaughter_records', function (Blueprint $table) {
            $table->dropColumn(['attachment_type', 'attachment_data']);
        });
    }
};
