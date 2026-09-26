<?php

namespace App\Policies;

use App\Models\AdmissionWave;
use App\Models\User;

class AdmissionWavePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admission_waves.view');
    }

    public function view(User $user, AdmissionWave $wave): bool
    {
        return $user->hasPermission('admission_waves.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('admission_waves.create');
    }

    public function update(User $user, AdmissionWave $wave): bool
    {
        return $user->hasPermission('admission_waves.update');
    }

    public function open(User $user, AdmissionWave $wave): bool
    {
        return $user->hasPermission('admission_waves.open');
    }

    public function close(User $user, AdmissionWave $wave): bool
    {
        return $user->hasPermission('admission_waves.close');
    }

    public function publishResults(User $user, AdmissionWave $wave): bool
    {
        return $user->hasPermission('admission_results.publish');
    }
}
