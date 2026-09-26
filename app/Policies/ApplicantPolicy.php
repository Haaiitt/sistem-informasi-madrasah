<?php

namespace App\Policies;

use App\Models\Applicant;
use App\Models\User;

class ApplicantPolicy
{
    // Orang tua: hanya miliknya sendiri (BR-06). Panitia/Admin TU: lewat permission.
    public function view(User $user, Applicant $applicant): bool
    {
        if ($user->hasRole('calon_siswa')) {
            return $applicant->user_id === $user->id;
        }

        return $user->hasPermission('applicants.view');
    }

    public function update(User $user, Applicant $applicant): bool
    {
        if ($user->hasRole('calon_siswa')) {
            return $applicant->user_id === $user->id && $applicant->isEditableByGuardian();
        }

        return false; // panitia mengubah status lewat alur verifikasi, bukan update biasa (langkah 4)
    }

    public function create(User $user): bool
    {
        return $user->hasRole('calon_siswa');
    }

    public function verify(User $user, Applicant $applicant): bool
    {
        return $user->hasPermission('applicants.verify');
    }

    public function decide(User $user, Applicant $applicant): bool
    {
        return $user->hasPermission('applicants.decide');
    }

    public function recordReenrollment(User $user, Applicant $applicant): bool
    {
        return $user->hasPermission('applicants.record_reenrollment');
    }

    // Panitia/Admin TU (lewat permission applicants.view) boleh melihat detail pendaftar mana pun,
    // beda dari method view() di atas yang khusus pengecekan kepemilikan orang tua.
    public function viewAsStaff(User $user): bool
    {
        return $user->hasPermission('applicants.view');
    }

    // UF-08: hanya Admin TU, bukan Panitia (pemisahan tugas).
    public function convert(User $user, Applicant $applicant): bool
    {
        return $user->hasPermission('applicants.convert_to_student');
    }

    public function export(User $user): bool
    {
        return $user->hasPermission('applicants.export');
    }
}
