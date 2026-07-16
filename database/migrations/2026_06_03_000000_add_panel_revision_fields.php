<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('group_members');
            $table->timestamp('rejected_at')->nullable()->after('rejection_reason');
        });

        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->timestamp('task_started_at')->nullable()->after('rejection_reason');
            $table->timestamp('task_completed_at')->nullable()->after('task_started_at');
            $table->unsignedInteger('task_duration_minutes')->nullable()->after('task_completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->dropColumn([
                'task_started_at',
                'task_completed_at',
                'task_duration_minutes',
            ]);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'rejected_at']);
        });
    }
};
