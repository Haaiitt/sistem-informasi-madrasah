<?php

namespace App\Policies;

use App\Models\User;

class PublicMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('public_messages.view');
    }

    public function reply(User $user): bool
    {
        return $user->hasPermission('public_messages.reply');
    }

    public function updateStatus(User $user): bool
    {
        return $user->hasPermission('public_messages.update_status');
    }
}
