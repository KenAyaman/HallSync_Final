<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->unsignedTinyInteger('satisfaction_rating')->nullable()->after('completion_note');
            $table->string('satisfaction_note', 280)->nullable()->after('satisfaction_rating');
            $table->timestamp('satisfaction_rated_at')->nullable()->after('satisfaction_note');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->dropColumn([
                'satisfaction_rating',
                'satisfaction_note',
                'satisfaction_rated_at',
            ]);
        });
    }
};
