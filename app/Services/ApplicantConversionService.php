<?php

namespace App\Services;

use App\Models\Applicant;
use App\Models\ApplicantStatusHistory;
use App\Models\AuditLog;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApplicantConversionService
{
    // BR-07: hanya dari daftar_ulang, hanya sekali, atomik.
    public function convert(Applicant $applicant, User $actor): Student
    {
        if ($applicant->status !== 'daftar_ulang') {
            throw ValidationException::withMessages(['status' => 'Hanya pendaftar berstatus daftar ulang yang dapat dikonversi.']);
        }

        if (Student::where('applicant_id', $applicant->id)->exists()) {
            throw ValidationException::withMessages(['status' => 'Pendaftar ini sudah pernah dikonversi menjadi siswa.']);
        }

        return DB::transaction(function () use ($applicant, $actor) {
            $student = Student::create([
                'applicant_id' => $applicant->id,
                'nisn' => $applicant->nisn,
                'nik' => $applicant->nik,
                'full_name' => $applicant->full_name,
                'gender' => $applicant->gender,
                'birth_place' => $applicant->birth_place,
                'birth_date' => $applicant->birth_date,
                'address' => $applicant->address,
                'village' => $applicant->village,
                'district' => $applicant->district,
                'city' => $applicant->city,
                'province' => $applicant->province,
                'postal_code' => $applicant->postal_code,
                'status' => 'aktif',
                'entered_on' => now()->toDateString(),
            ]);

            foreach ($applicant->guardians as $applicantGuardian) {
                $student->guardians()->create([
                    'relation' => $applicantGuardian->relation,
                    'full_name' => $applicantGuardian->full_name,
                    'occupation' => $applicantGuardian->occupation,
                    'phone' => $applicantGuardian->phone,
                    'is_primary_contact' => $applicantGuardian->is_primary_contact,
                ]);
            }

            $applicant->update(['status' => 'menjadi_siswa']);

            ApplicantStatusHistory::create([
                'applicant_id' => $applicant->id, 'from_status' => 'daftar_ulang', 'to_status' => 'menjadi_siswa',
                'changed_by' => $actor->id,
            ]);

            AuditLog::create([
                'user_id' => $actor->id, 'action' => 'applicants.converted_to_student',
                'auditable_type' => Student::class, 'auditable_id' => $student->id,
                'new_values' => ['applicant_id' => $applicant->id],
                'ip_address' => request()->ip(),
            ]);

            return $student;
        });
    }
}
