<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $addRoomNumber = ! Schema::hasColumn('users', 'room_number');
        $addIsActive = ! Schema::hasColumn('users', 'is_active');

        if ($addRoomNumber || $addIsActive) {
            Schema::table('users', function (Blueprint $table) use ($addRoomNumber, $addIsActive) {
                if ($addRoomNumber) {
                    $table->string('room_number', 20)->nullable()->after('role');
                }

                if ($addIsActive) {
                    $table->boolean('is_active')->default(true)->after('room_number');
                }
            });
        }
    }

    public function down(): void
    {
        $columns = array_filter([
            Schema::hasColumn('users', 'room_number') ? 'room_number' : null,
            Schema::hasColumn('users', 'is_active') ? 'is_active' : null,
        ]);

        Schema::table('users', function (Blueprint $table) use ($columns) {
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
