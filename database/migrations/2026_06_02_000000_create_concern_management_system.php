<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('concerns', function (Blueprint $table) {
            $table->string('concern_id', 32)->nullable()->unique()->after('id');
            $table->string('priority', 16)->default('medium')->after('status');
            $table->boolean('is_anonymous')->default(false)->after('priority');
            $table->timestamp('submitted_at')->nullable()->after('replied_at');
            $table->timestamp('review_started_at')->nullable()->after('submitted_at');
            $table->timestamp('resolved_at')->nullable()->after('review_started_at');
            $table->timestamp('closed_at')->nullable()->after('resolved_at');
            $table->timestamp('due_at')->nullable()->after('closed_at');
            $table->unsignedTinyInteger('reopen_count')->default(0)->after('due_at');
            $table->text('resolution_notes')->nullable()->after('reopen_count');
            $table->index(['priority', 'status'], 'concerns_priority_status_idx');
            $table->index(['category', 'created_at'], 'concerns_category_created_at_idx');
            $table->index(['handled_by', 'status'], 'concerns_handler_status_idx');
        });

        Schema::create('concern_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concern_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['concern_id', 'created_at']);
        });

        Schema::create('concern_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concern_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('disk', 24)->default('local');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('size');
            $table->string('sha256', 64);
            $table->timestamps();
            $table->index(['concern_id', 'created_at']);
        });

        Schema::create('concern_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concern_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('assignment_role', 80)->default('Staff Member');
            $table->text('notes')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            $table->index(['concern_id', 'ended_at']);
            $table->index(['assigned_to', 'ended_at']);
        });

        Schema::create('concern_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concern_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status', 40)->nullable();
            $table->string('to_status', 40);
            $table->text('reason')->nullable();
            $table->timestamps();
            $table->index(['concern_id', 'created_at']);
        });

        Schema::create('concern_internal_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concern_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note');
            $table->timestamps();
            $table->index(['concern_id', 'created_at']);
        });

        Schema::create('concern_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concern_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 80);
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['concern_id', 'created_at']);
            $table->index(['actor_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concern_audit_logs');
        Schema::dropIfExists('concern_internal_notes');
        Schema::dropIfExists('concern_status_histories');
        Schema::dropIfExists('concern_assignments');
        Schema::dropIfExists('concern_evidence');
        Schema::dropIfExists('concern_messages');

        Schema::table('concerns', function (Blueprint $table) {
            $table->dropIndex('concerns_priority_status_idx');
            $table->dropIndex('concerns_category_created_at_idx');
            $table->dropIndex('concerns_handler_status_idx');
            $table->dropUnique(['concern_id']);
            $table->dropColumn([
                'concern_id',
                'priority',
                'is_anonymous',
                'submitted_at',
                'review_started_at',
                'resolved_at',
                'closed_at',
                'due_at',
                'reopen_count',
                'resolution_notes',
            ]);
        });
    }
};
