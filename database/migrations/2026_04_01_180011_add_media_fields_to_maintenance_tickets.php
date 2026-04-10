<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('photo');
            $table->string('video_path')->nullable()->after('image_path');
            // Drop old photo column if exists and replace with new structure
            if (Schema::hasColumn('maintenance_tickets', 'photo')) {
                $table->dropColumn('photo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_tickets', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'video_path']);
            $table->string('photo')->nullable();
        });
    }
};