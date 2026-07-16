<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_slot_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('facility_name');
            $table->dateTime('booking_date');
            $table->timestamps();

            $table->unique(['facility_name', 'booking_date'], 'booking_claims_facility_slot_unique');
            $table->unique(['user_id', 'booking_date'], 'booking_claims_user_slot_unique');
        });

        DB::table('bookings')
            ->where('status', 'approved')
            ->where('end_time', '>', now())
            ->orderBy('id')
            ->each(function ($booking) {
                DB::table('booking_slot_claims')->insert([
                    'booking_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'facility_name' => $booking->facility_name,
                    'booking_date' => $booking->booking_date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_slot_claims');
    }
};
