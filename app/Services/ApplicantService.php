<?php

namespace App\Services;

use App\Models\Applicant;
// use App\Models\ApplicantStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\AcademicYear;
use App\Models\AdmissionWave;
use App\Models\ApplicantStatusHistory;

class ApplicantService
{
    public function __construct(private ApplicantStatusMachine $statusMachine)
    {
    }

    public function createDraft(User $owner, array $data): Applicant
    {
        return DB::transaction(function () use ($owner, $data) {
            $applicant = Applicant::create([
                'user_id' => $owner->id,
                'type' => $data['type'],
                // BR-17: 'baru' selalu kelas tujuan 1.
                'target_grade' => $data['type'] === 'baru' ? 1 : $data['target_grade'],
                'status' => 'draft',
                'full_name' => $data['full_name'],
                'gender' => $data['gender'],
                'birth_place' => $data['birth_place'],
                'birth_date' => $data['birth_date'],
                'nisn' => $data['nisn'] ?? null,
                'nik' => $data['nik'] ?? null,
                'address' => $data['address'],
                'village' => $data['village'] ?? null,
                'district' => $data['district'] ?? null,
                'city' => $data['city'] ?? null,
                'province' => $data['province'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'previous_school_name' => $data['type'] === 'pindahan' ? $data['previous_school_name'] : null,
                'previous_grade' => $data['type'] === 'pindahan' ? $data['previous_grade'] : null,
            ]);

            $this->syncGuardians($applicant, $data['guardians']);

            return $applicant;
        });
    }

    public function updateDraft(Applicant $applicant, array $data): Applicant
    {
        if (! $applicant->isEditableByGuardian()) {
            throw ValidationException::withMessages(['status' => 'Pendaftaran ini sudah terkunci dan tidak dapat diubah.']);
        }

        return DB::transaction(function () use ($applicant, $data) {
            $applicant->update([
                'target_grade' => $applicant->type === 'baru' ? 1 : $data['target_grade'],
                'full_name' => $data['full_name'],
                'gender' => $data['gender'],
                'birth_place' => $data['birth_place'],
                'birth_date' => $data['birth_date'],
                'nisn' => $data['nisn'] ?? null,
                'nik' => $data['nik'] ?? null,
                'address' => $data['address'],
                'village' => $data['village'] ?? null,
                'district' => $data['district'] ?? null,
                'city' => $data['city'] ?? null,
                'province' => $data['province'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'previous_school_name' => $applicant->type === 'pindahan' ? $data['previous_school_name'] : null,
                'previous_grade' => $applicant->type === 'pindahan' ? $data['previous_grade'] : null,
            ]);

            $this->syncGuardians($applicant, $data['guardians']);

            return $applicant;
        });
    }

    // FR-PPDB-18 / BR-04 tahap 1: pemberitahuan dini, tidak mengikat, pesan generik.
    public function nisnLooksAvailable(string $nisn, ?int $academicYearId, ?int $ignoreApplicantId = null): bool
    {
        if (! $academicYearId) {
            return true; // belum ada tahun ajaran terpasang (draft baru); dicek ulang saat kirim.
        }

        return ! Applicant::where('academic_year_id', $academicYearId)
            ->where('nisn', $nisn)
            ->whereIn('status', ['dikirim', 'perlu_perbaikan', 'terverifikasi', 'diterima', 'daftar_ulang', 'menjadi_siswa'])
            ->when($ignoreApplicantId, fn ($q) => $q->where('id', '!=', $ignoreApplicantId))
            ->exists();
    }

    public function submit(Applicant $applicant, ApplicantDocumentService $documents): void
    {
        if (! $this->statusMachine->canTransition($applicant->status, 'dikirim')) {
            throw ValidationException::withMessages(['status' => 'Pendaftaran ini tidak dapat dikirim.']);
        }

        $missing = $documents->missingRequired($applicant);
        if (! empty($missing)) {
            throw ValidationException::withMessages(['documents' => 'Berkas wajib belum lengkap: '.implode(', ', $missing).'.']);
        }

        if ($applicant->guardians()->count() < 1) {
            throw ValidationException::withMessages(['guardians' => 'Data wali wajib diisi minimal satu.']);
        }

        DB::transaction(function () use ($applicant) {
            $fromStatus = $applicant->status;
            $isFirstSubmission = $fromStatus === 'draft';

            if ($isFirstSubmission) {
                // BR-01: harus ada gelombang dibuka saat kirim pertama kali.
                $wave = AdmissionWave::where('status', 'dibuka')->lockForUpdate()->first();
                if (! $wave) {
                    throw ValidationException::withMessages(['status' => 'Tidak ada gelombang dibuka saat ini. Pendaftaran tidak dapat dikirim.']);
                }

                if ($applicant->nisn) {
                    $this->assertNisnAvailableBinding($applicant, $wave->academic_year_id);
                }

                $applicant->admission_wave_id = $wave->id;
                $applicant->academic_year_id = $wave->academic_year_id;
                $applicant->registration_number = $this->generateRegistrationNumber($wave->academic_year_id);
            } else {
                // BR-16: kirim ulang dari perlu_perbaikan TIDAK bergantung status gelombang, tanpa batas waktu.
                if ($applicant->nisn) {
                    $this->assertNisnAvailableBinding($applicant, $applicant->academic_year_id);
                }
            }

            $applicant->status = 'dikirim';
            $applicant->submitted_at = now();
            $applicant->save();

            ApplicantStatusHistory::create([
                'applicant_id' => $applicant->id,
                'from_status' => $fromStatus,
                'to_status' => 'dikirim',
                'changed_by' => $applicant->user_id,
            ]);
        });
    }

    // BR-04 tahap 2: pemeriksaan MENGIKAT, dalam transaksi + row lock (dua request bersamaan tidak lolos dua-duanya).
    private function assertNisnAvailableBinding(Applicant $applicant, int $academicYearId): void
    {
        $exists = Applicant::where('academic_year_id', $academicYearId)
            ->where('nisn', $applicant->nisn)
            ->whereIn('status', ['dikirim', 'perlu_perbaikan', 'terverifikasi', 'diterima', 'daftar_ulang', 'menjadi_siswa'])
            ->where('id', '!=', $applicant->id)
            ->lockForUpdate()
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages(['nisn' => 'NISN tidak dapat dipakai.']);
        }
    }

    private function generateRegistrationNumber(int $academicYearId): string
    {
        $year = AcademicYear::find($academicYearId)->starts_on->year;
        $count = Applicant::where('academic_year_id', $academicYearId)->whereNotNull('registration_number')->lockForUpdate()->count();
        $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        return "PPDB-{$year}-{$sequence}";
    }

    private function syncGuardians(Applicant $applicant, array $guardians): void
    {
        $applicant->guardians()->delete();

        foreach ($guardians as $guardian) {
            $applicant->guardians()->create([
                'relation' => $guardian['relation'],
                'full_name' => $guardian['full_name'],
                'occupation' => $guardian['occupation'] ?? null,
                'phone' => $guardian['phone'] ?? null,
                'is_primary_contact' => $guardian['is_primary_contact'] ?? false,
            ]);
        }
    }
}
