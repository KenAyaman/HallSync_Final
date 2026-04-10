<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement("
            ALTER TABLE maintenance_tickets
            MODIFY status ENUM('received', 'pending_approval', 'approved', 'rejected', 'assigned', 'in_progress', 'completed')
            NOT NULL DEFAULT 'received'
        ");

        DB::table('maintenance_tickets')
            ->where('status', 'received')
            ->update(['status' => 'pending_approval']);

        DB::statement("
            ALTER TABLE maintenance_tickets
            MODIFY status ENUM('pending_approval', 'approved', 'rejected', 'assigned', 'in_progress', 'completed')
            NOT NULL DEFAULT 'pending_approval'
        ");
    }

    public function down()
    {
        DB::statement("
            ALTER TABLE maintenance_tickets
            MODIFY status ENUM('received', 'pending_approval', 'approved', 'rejected', 'assigned', 'in_progress', 'completed')
            NOT NULL DEFAULT 'pending_approval'
        ");

        DB::table('maintenance_tickets')
            ->where('status', 'pending_approval')
            ->update(['status' => 'received']);

        DB::statement("
            ALTER TABLE maintenance_tickets
            MODIFY status ENUM('received', 'assigned', 'in_progress', 'completed')
            NOT NULL DEFAULT 'received'
        ");
    }
};
