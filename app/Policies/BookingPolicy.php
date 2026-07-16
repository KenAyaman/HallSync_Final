<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->isManager() || $booking->user_id === $user->id;
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->isResident() && $booking->user_id === $user->id && $booking->status === Booking::STATUS_APPROVED;
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $this->update($user, $booking);
    }
}
