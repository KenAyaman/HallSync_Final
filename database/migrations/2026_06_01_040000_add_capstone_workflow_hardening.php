<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resident_roster_entries', function (Blueprint $table) {
            $table->id();
            $table->string('resident_number', 40)->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('room_number', 20);
            $table->boolean('is_active')->default(true);
            $table->foreignId('claimed_by_user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('residency_status', 20)->default('active')->after('is_active');
            $table->timestamp('moved_out_at')->nullable()->after('deactivated_at');
        });

        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->unsignedTinyInteger('reopen_count')->default(0);
            $table->timestamp('cancellation_requested_at')->nullable();
            $table->text('cancellation_reason')->nullable();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE maintenance_tickets
                MODIFY status ENUM(
                    'received', 'pending_approval', 'approved', 'rejected',
                    'assigned', 'in_progress', 'completed', 'resolved', 'closed', 'cancelled'
                ) NOT NULL DEFAULT 'pending_approval'
            ");
        }

        DB::table('maintenance_tickets')
            ->where('status', 'completed')
            ->update([
                'status' => 'resolved',
                'resolved_at' => DB::raw('updated_at'),
            ]);
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('maintenance_tickets')
                ->whereIn('status', ['resolved', 'closed', 'cancelled'])
                ->update(['status' => 'completed']);

            DB::statement("
                ALTER TABLE maintenance_tickets
                MODIFY status ENUM(
                    'received', 'pending_approval', 'approved', 'rejected',
                    'assigned', 'in_progress', 'completed'
                ) NOT NULL DEFAULT 'pending_approval'
            ");
        }

        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->dropColumn([
                'resolved_at',
                'closed_at',
                'reopen_count',
                'cancellation_requested_at',
                'cancellation_reason',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['residency_status', 'moved_out_at']);
        });

        Schema::dropIfExists('resident_roster_entries');
    }
};
