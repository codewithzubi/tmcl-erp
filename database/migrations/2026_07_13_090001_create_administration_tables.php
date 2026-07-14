<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->foreignId('role_id')->nullable()->after('username')->constrained()->nullOnDelete();
            $table->string('department')->nullable()->after('role_id');
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('department');
            $table->timestamp('last_login')->nullable()->after('status');
        });

        Schema::create('event_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('role')->nullable();
            $table->string('module');
            $table->string('screen');
            $table->string('record_id')->nullable();
            $table->enum('action', ['Create', 'Update', 'Delete', 'Approve', 'Reject', 'Login', 'Logout']);
            $table->text('new_value')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('device_info')->nullable();
            $table->timestamp('logged_at');
            $table->timestamps();
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('time_zone');
            $table->string('date_format');
            $table->string('default_currency');
            $table->string('language');
            $table->timestamps();
        });

        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('category', ['Event', 'Stock', 'Alert']);
            $table->string('title');
            $table->text('description');
            $table->boolean('read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('event_logs');
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn(['username', 'department', 'status', 'last_login']);
        });
        Schema::dropIfExists('roles');
    }
};
