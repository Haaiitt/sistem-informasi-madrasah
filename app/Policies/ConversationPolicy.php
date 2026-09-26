<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    // BR-21: hanya penerimanya (peserta pemilik) dan admin berwenang.
    public function view(User $user, Conversation $conversation): bool
    {
        if ($user->hasRole('calon_siswa')) {
            return $conversation->user_id === $user->id;
        }

        return $user->hasPermission('messages.view_threads');
    }

    public function reply(User $user, Conversation $conversation): bool
    {
        return $this->view($user, $conversation);
    }

    public function startAsGuardian(User $user): bool
    {
        return $user->hasRole('calon_siswa');
    }

    public function startAsStaff(User $user): bool
    {
        return $user->hasPermission('messages.send_individual');
    }
}
