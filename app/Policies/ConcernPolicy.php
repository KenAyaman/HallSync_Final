<?php

namespace App\Policies;

use App\Models\Concern;
use App\Models\User;

class ConcernPolicy
{
    public function view(User $user, Concern $concern): bool
    {
        return $user->isManager() || ($user->isResident() && $concern->user_id === $user->id);
    }

    public function update(User $user, Concern $concern): bool
    {
        return $user->isResident() && $concern->user_id === $user->id && $concern->isEditableByResident();
    }

    public function delete(User $user, Concern $concern): bool
    {
        return $this->update($user, $concern);
    }

    public function manage(User $user, Concern $concern): bool
    {
        return $user->isManager();
    }
}
