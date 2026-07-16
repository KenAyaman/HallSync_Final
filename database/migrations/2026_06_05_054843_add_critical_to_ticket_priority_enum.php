<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite does not enforce ENUM constraints — no DDL change needed.
        // On MySQL the column must be ALTERed to include 'critical'.
        if ($this->isMysql()) {
            DB::statement("
                ALTER TABLE maintenance_tickets
                MODIFY priority ENUM('low', 'medium', 'high', 'urgent', 'critical')
                NOT NULL DEFAULT 'medium'
            ");
        }
    }

    public function down(): void
    {
        if ($this->isMysql()) {
            // Remap any existing 'critical' rows back to 'urgent' before shrinking the enum.
            DB::table('maintenance_tickets')
                ->where('priority', 'critical')
                ->update(['priority' => 'urgent']);

            DB::statement("
                ALTER TABLE maintenance_tickets
                MODIFY priority ENUM('low', 'medium', 'high', 'urgent')
                NOT NULL DEFAULT 'medium'
            ");
        }
    }

    private function isMysql(): bool
    {
        return DB::getDriverName() === 'mysql';
    }
};
