<?php

namespace App\Policies;

use App\Models\MaintenanceTicket;
use App\Models\User;

class MaintenanceTicketPolicy
{
    public function view(User $user, MaintenanceTicket $ticket): bool
    {
        return $user->isManager()
            || $ticket->user_id === $user->id
            || ($user->isHandyman() && $ticket->assigned_to === $user->id);
    }

    public function update(User $user, MaintenanceTicket $ticket): bool
    {
        return $user->isManager()
            || ($user->isResident()
                && $ticket->user_id === $user->id
                && $ticket->status === MaintenanceTicket::STATUS_PENDING_APPROVAL);
    }

    public function delete(User $user, MaintenanceTicket $ticket): bool
    {
        return $ticket->status === MaintenanceTicket::STATUS_PENDING_APPROVAL
            && ($user->isManager() || ($user->isResident() && $ticket->user_id === $user->id));
    }

    public function reopen(User $user, MaintenanceTicket $ticket): bool
    {
        return $user->isResident() && $ticket->user_id === $user->id;
    }

    public function close(User $user, MaintenanceTicket $ticket): bool
    {
        return $user->isResident() && $ticket->user_id === $user->id;
    }

    public function requestCancellation(User $user, MaintenanceTicket $ticket): bool
    {
        return $user->isResident() && $ticket->user_id === $user->id;
    }

    public function manage(User $user): bool
    {
        return $user->isManager();
    }
}
