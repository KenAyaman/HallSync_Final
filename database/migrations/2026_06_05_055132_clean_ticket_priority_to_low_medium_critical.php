<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remap legacy values to the canonical three-tier system.
        DB::table('maintenance_tickets')
            ->where('priority', 'high')
            ->update(['priority' => 'medium']);

        DB::table('maintenance_tickets')
            ->where('priority', 'urgent')
            ->update(['priority' => 'critical']);

        // On MySQL, shrink the ENUM to the three values the app actually uses.
        if ($this->isMysql()) {
            DB::statement("
                ALTER TABLE maintenance_tickets
                MODIFY priority ENUM('low', 'medium', 'critical')
                NOT NULL DEFAULT 'medium'
            ");
        }
    }

    public function down(): void
    {
        if ($this->isMysql()) {
            DB::statement("
                ALTER TABLE maintenance_tickets
                MODIFY priority ENUM('low', 'medium', 'high', 'urgent', 'critical')
                NOT NULL DEFAULT 'medium'
            ");
        }
    }

    private function isMysql(): bool
    {
        return DB::getDriverName() === 'mysql';
    }
};
