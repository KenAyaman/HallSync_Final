<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('maintenance_tickets')
            ->where('status', 'received')
            ->update(['status' => 'pending_approval']);

        if ($this->usesMysqlEnumSyntax()) {
            DB::statement("
                ALTER TABLE maintenance_tickets
                MODIFY status ENUM('received', 'pending_approval', 'approved', 'rejected', 'assigned', 'in_progress', 'completed')
                NOT NULL DEFAULT 'received'
            ");

            DB::statement("
                ALTER TABLE maintenance_tickets
                MODIFY status ENUM('pending_approval', 'approved', 'rejected', 'assigned', 'in_progress', 'completed')
                NOT NULL DEFAULT 'pending_approval'
            ");
        }
    }

    public function down(): void
    {
        DB::table('maintenance_tickets')
            ->where('status', 'pending_approval')
            ->update(['status' => 'received']);

        if ($this->usesMysqlEnumSyntax()) {
            DB::statement("
                ALTER TABLE maintenance_tickets
                MODIFY status ENUM('received', 'pending_approval', 'approved', 'rejected', 'assigned', 'in_progress', 'completed')
                NOT NULL DEFAULT 'pending_approval'
            ");

            DB::statement("
                ALTER TABLE maintenance_tickets
                MODIFY status ENUM('received', 'assigned', 'in_progress', 'completed')
                NOT NULL DEFAULT 'received'
            ");
        }
    }

    private function usesMysqlEnumSyntax(): bool
    {
        return DB::getDriverName() === 'mysql';
    }
};
