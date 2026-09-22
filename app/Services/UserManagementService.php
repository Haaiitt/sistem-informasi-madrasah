<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserManagementService
{
    public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'username' => strtolower($data['username']),
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'is_active' => true,
        ]);
    }

    public function update(User $user, array $data): User
    {
        $user->update([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
        ]);

        return $user;
    }

    // FR-AUTH-04: nonaktifkan, bukan hapus (database.md: users Delete ✗).
    public function deactivate(User $user): void
    {
        $user->update(['is_active' => false]);
    }

    public function activate(User $user): void
    {
        $user->update(['is_active' => true]);
    }

    // FR-AUTH-05 + DB-04: perubahan role dicatat di audit_logs.
    public function assignRoles(User $user, array $roleIds, User $actor): void
    {
        $before = $user->roles()->pluck('name')->all();

        DB::transaction(function () use ($user, $roleIds) {
            $user->syncRoles($roleIds);
        });

        $after = $user->roles()->pluck('name')->all();

        AuditLog::create([
            'user_id' => $actor->id,
            'action' => 'users.roles_assigned',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'old_values' => ['roles' => $before],
            'new_values' => ['roles' => $after],
            'ip_address' => request()->ip(),
        ]);
    }
}
