<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_reads', function (Blueprint $table) {
            $table->string('notification_status')->nullable()->after('notification_id');
            $table->dropUnique('notification_reads_unique');
            $table->unique(
                ['user_id', 'notification_type', 'notification_id', 'notification_status'],
                'notification_reads_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('notification_reads', function (Blueprint $table) {
            $table->dropUnique('notification_reads_unique');
            $table->unique(['user_id', 'notification_type', 'notification_id'], 'notification_reads_unique');
            $table->dropColumn('notification_status');
        });
    }
};
