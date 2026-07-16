<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CANONICAL role-column migration (M-12).
// The earlier file 2026_03_27_022825_add_role_to_users_table.php is a no-op duplicate.
// up() is guarded so fresh installs that already have the column via another path are safe.
// down() carries the matching guard so rollback never fails if up() was skipped.
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('resident')->after('password');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};