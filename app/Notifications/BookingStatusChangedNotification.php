<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class BookingStatusChangedNotification extends Notification
{
    public function __construct(
        private readonly Booking $booking,
        private readonly string $event
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $facilityName = $this->booking->facility_name ?? 'facility';
        $date = $this->booking->booking_date?->format('M d, Y') ?? '';

        $message = "Your booking for {$facilityName} on {$date} has been cancelled.";

        return [
            'title' => 'Booking Cancelled',
            'message' => $message,
            'booking_id' => $this->booking->id,
            'event' => $this->event,
            'status' => $this->booking->status,
        ];
    }
}
