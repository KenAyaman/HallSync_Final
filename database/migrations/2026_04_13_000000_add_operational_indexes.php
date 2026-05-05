<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['facility_name', 'booking_date', 'status'], 'bookings_facility_date_status_idx');
            $table->index(['user_id', 'booking_date'], 'bookings_user_date_idx');
        });

        Schema::table('community_posts', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'community_posts_status_created_at_idx');
            $table->index(['user_id', 'status'], 'community_posts_user_status_idx');
        });

        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->index(['status', 'assigned_to'], 'maintenance_tickets_status_assigned_to_idx');
            $table->index(['user_id', 'created_at'], 'maintenance_tickets_user_created_at_idx');
        });

        Schema::table('concerns', function (Blueprint $table) {
            $table->index(['status', 'replied_at'], 'concerns_status_replied_at_idx');
            $table->index(['user_id', 'created_at'], 'concerns_user_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_facility_date_status_idx');
            $table->dropIndex('bookings_user_date_idx');
        });

        Schema::table('community_posts', function (Blueprint $table) {
            $table->dropIndex('community_posts_status_created_at_idx');
            $table->dropIndex('community_posts_user_status_idx');
        });

        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->dropIndex('maintenance_tickets_status_assigned_to_idx');
            $table->dropIndex('maintenance_tickets_user_created_at_idx');
        });

        Schema::table('concerns', function (Blueprint $table) {
            $table->dropIndex('concerns_status_replied_at_idx');
            $table->dropIndex('concerns_user_created_at_idx');
        });
    }
};
