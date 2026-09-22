<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('users.view');
    }

    public function view(User $user, User $target): bool
    {
        return $user->hasPermission('users.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('users.create');
    }

    public function update(User $user, User $target): bool
    {
        return $user->hasPermission('users.update');
    }

    // FR-AUTH-04: nonaktifkan akun, bukan hapus (database.md: users Delete ✗).
    public function deactivate(User $user, User $target): bool
    {
        return $user->hasPermission('users.deactivate');
    }

    // FR-AUTH-05: menetapkan role pengguna.
    public function assignRoles(User $user, User $target): bool
    {
        return $user->hasPermission('roles.assign');
    }

    // FR-AUTH-06: mengatur ulang kata sandi pengguna.
    public function issueResetLink(User $user, User $target): bool
    {
        return $user->hasPermission('users.issue_reset_link');
    }
}
