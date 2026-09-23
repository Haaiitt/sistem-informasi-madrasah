<?php

namespace App\Policies;

use App\Models\User;

class SettingPolicy
{
    public function view(User $user): bool
    {
        return $user->hasPermission('site_settings.view');
    }

    public function update(User $user): bool
    {
        return $user->hasPermission('site_settings.update');
    }
}
