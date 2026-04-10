<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->enum('status', ['pending_approval', 'approved', 'rejected', 'assigned', 'in_progress', 'completed'])
                ->default('pending_approval')
                ->change();
        });
    }

    public function down()
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->enum('status', ['received', 'assigned', 'in_progress', 'completed'])
                ->default('received')
                ->change();
        });
    }
};