<?php

namespace App\Policies;

use App\Models\User;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('messages.view_threads');
    }

    public function sendBroadcast(User $user): bool
    {
        return $user->hasPermission('messages.send_broadcast');
    }
}
