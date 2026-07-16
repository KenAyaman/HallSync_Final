<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->timestamp('starts_at')->nullable()->after('is_active');
            $table->timestamp('expires_at')->nullable()->after('starts_at');
            $table->boolean('is_pinned')->default(false)->after('expires_at');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('group_members', 500)->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['starts_at', 'expires_at', 'is_pinned']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('group_members');
        });
    }
};
