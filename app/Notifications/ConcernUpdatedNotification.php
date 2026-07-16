<?php

namespace App\Notifications;

use App\Models\Concern;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConcernUpdatedNotification extends Notification
{
    public function __construct(
        private readonly Concern $concern,
        private readonly string $event, // 'replied' | 'status_changed'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'id' => $this->concern->id,
            'concern_id' => $this->concern->concern_id,
            'subject' => $this->concern->subject,
            'event' => $this->event,
            'concern_status' => $this->concern->status,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->event === 'replied'
            ? 'Administration has replied to your concern'
            : 'Your concern status has been updated';

        $intro = $this->event === 'replied'
            ? 'Administration has sent a reply regarding your private concern. Please log in to read the update and respond if needed.'
            : 'The status of your private concern has been updated to: ' . $this->concern->status_label . '.';

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line($intro)
            ->line('**Concern:** ' . $this->concern->subject)
            ->line('**Reference:** ' . $this->concern->concern_id)
            ->action('View Concern', route('concerns.show', $this->concern))
            ->line('Your concern is handled privately. Thank you for using HallSync.');
    }
}
