<?php

namespace App\Policies;

use App\Models\CommunityPost;
use App\Models\User;

class CommunityPostPolicy
{
    public function update(User $user, CommunityPost $post): bool
    {
        // Only residents can edit their own posts (L-08).
        return $user->isResident() && $post->user_id === $user->id;
    }

    public function delete(User $user, CommunityPost $post): bool
    {
        return $user->isManager() || $post->user_id === $user->id;
    }

    public function moderate(User $user): bool
    {
        return $user->isManager();
    }
}
