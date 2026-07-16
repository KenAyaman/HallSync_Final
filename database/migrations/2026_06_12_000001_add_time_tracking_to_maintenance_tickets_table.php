<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('maintenance_tickets', 'work_started_at')) {
                $table->timestamp('work_started_at')->nullable()->after('task_started_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('maintenance_tickets', 'work_started_at')) {
                $table->dropColumn('work_started_at');
            }
        });
    }
};
