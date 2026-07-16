<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The original community_posts migration created `approved_by` without nullOnDelete().
     * A second migration tried to fix it but the `hasColumn()` guard skipped the correction.
     * This migration re-creates the constraint with ON DELETE SET NULL on MySQL (C-09).
     * SQLite does not enforce FK constraints so no change is needed there.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // Drop the existing constraint and re-add it with ON DELETE SET NULL.
        DB::statement('ALTER TABLE community_posts DROP FOREIGN KEY community_posts_approved_by_foreign');
        DB::statement(
            'ALTER TABLE community_posts ADD CONSTRAINT community_posts_approved_by_foreign
             FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL'
        );
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE community_posts DROP FOREIGN KEY community_posts_approved_by_foreign');
        DB::statement(
            'ALTER TABLE community_posts ADD CONSTRAINT community_posts_approved_by_foreign
             FOREIGN KEY (approved_by) REFERENCES users(id)'
        );
    }
};
