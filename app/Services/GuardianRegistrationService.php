<?php

namespace App\Services;

use App\Models\AdmissionWave;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GuardianRegistrationService
{
    // UF-05: registrasi hanya saat ada gelombang berstatus 'dibuka' (D-20).
    public function waveIsOpen(): bool
    {
        return AdmissionWave::where('status', 'dibuka')->exists();
    }

    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'username' => strtolower($data['username']),
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'is_active' => true, // UF-05: akun langsung aktif, tanpa verifikasi email
        ]);

        $roleId = Role::where('name', 'calon_siswa')->value('id');
        $user->syncRoles([$roleId]);

        return $user;
    }
}
