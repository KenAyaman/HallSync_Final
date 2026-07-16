<?php

namespace App\Notifications;

use App\Models\MaintenanceTicket;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusChangedNotification extends Notification
{
    public function __construct(
        private readonly MaintenanceTicket $ticket,
        private readonly string $event,  // 'approved' | 'rejected' | 'assigned' | 'resolved' | 'cancelled'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'id' => $this->ticket->id,
            'ticket_id' => $this->ticket->ticket_id,
            'title' => $this->ticket->title,
            'event' => $this->event,
            'status' => $this->ticket->status,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = match ($this->event) {
            'approved'  => 'Your maintenance request has been approved',
            'rejected'  => 'Your maintenance request was not approved',
            'assigned'  => 'A staff member has been assigned to your request',
            'resolved'  => 'Your maintenance request has been resolved',
            'cancelled' => 'Your maintenance request has been cancelled',
            default     => 'Update on your maintenance request',
        };

        $intro = match ($this->event) {
            'approved'  => 'Good news — your maintenance request has been reviewed and approved.',
            'rejected'  => 'Your maintenance request has been reviewed and could not be approved at this time.',
            'assigned'  => 'A staff member has been assigned and will attend to your request shortly.',
            'resolved'  => 'The maintenance work on your request is complete. Please confirm or reopen within 7 days.',
            'cancelled' => 'Your maintenance request cancellation has been processed.',
            default     => 'There is an update on your maintenance request.',
        };

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line($intro)
            ->line('**Request:** ' . $this->ticket->title)
            ->line('**Reference:** ' . $this->ticket->ticket_id);

        if ($this->event === 'rejected' && $this->ticket->rejection_reason) {
            $mail->line('**Reason:** ' . $this->ticket->rejection_reason);
        }

        $mail->action('View Request', route('tickets.show', $this->ticket));
        $mail->line('Thank you for using HallSync.');

        return $mail;
    }
}
