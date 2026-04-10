<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('maintenance_tickets', function (Blueprint $table) {
        if (!Schema::hasColumn('maintenance_tickets', 'assigned_to')) {
            $table->foreignId('assigned_to')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
        }
    });
}
};
