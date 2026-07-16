<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->whereNull('is_active')->update(['is_active' => true]);

        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number', 30)->nullable()->after('room_number');
            $table->timestamp('deactivated_at')->nullable()->after('is_active');
            $table->boolean('must_change_password')->default(false)->after('password');
            $table->timestamp('password_reset_at')->nullable()->after('must_change_password');

            $table->index(['role', 'is_active'], 'users_role_active_idx');
            $table->index('created_at', 'users_created_at_idx');
        });

        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 80)->index();
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['subject_user_id', 'created_at'], 'user_activity_subject_created_idx');
            $table->index(['actor_user_id', 'created_at'], 'user_activity_actor_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_activity_logs');

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_active_idx');
            $table->dropIndex('users_created_at_idx');
            $table->dropColumn([
                'phone_number',
                'deactivated_at',
                'must_change_password',
                'password_reset_at',
            ]);
        });
    }
};
