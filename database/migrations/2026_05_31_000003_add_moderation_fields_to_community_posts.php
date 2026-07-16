<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            if (! Schema::hasColumn('community_posts', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('video_path');
            }

            if (! Schema::hasColumn('community_posts', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('rejection_reason');
            }

            if (! Schema::hasColumn('community_posts', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            if (Schema::hasColumn('community_posts', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }

            foreach (['approved_at', 'rejection_reason'] as $column) {
                if (Schema::hasColumn('community_posts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
