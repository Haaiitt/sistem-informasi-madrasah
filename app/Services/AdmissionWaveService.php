<?php

namespace App\Services;

use App\Models\AdmissionWave;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdmissionWaveService
{
    public function create(array $data, User $actor): AdmissionWave
    {
        return DB::transaction(function () use ($data, $actor) {
            $this->assertNoOverlap($data['academic_year_id'], $data['opens_at'], $data['closes_at'] ?? null);

            $wave = AdmissionWave::create([
                'academic_year_id' => $data['academic_year_id'],
                'name' => $data['name'],
                'opens_at' => $data['opens_at'],
                'closes_at' => $data['closes_at'] ?? null,
                'status' => 'dijadwalkan', // BR-02: penjadwal yang mengubah status, bukan dibuat langsung terbuka
                'created_by' => $actor->id,
            ]);

            $this->logStatusChange($wave, null, 'dijadwalkan', $actor);

            return $wave;
        });
    }

    public function update(AdmissionWave $wave, array $data): AdmissionWave
    {
        return DB::transaction(function () use ($wave, $data) {
            $this->assertNoOverlap(
                $data['academic_year_id'],
                $data['opens_at'],
                $data['closes_at'] ?? null,
                ignoreWaveId: $wave->id
            );

            $wave->update([
                'academic_year_id' => $data['academic_year_id'],
                'name' => $data['name'],
                'opens_at' => $data['opens_at'],
                'closes_at' => $data['closes_at'] ?? null,
            ]);

            return $wave;
        });
    }

    // FR-PPDB-02: buka manual.
    public function openManually(AdmissionWave $wave, User $actor): void
    {
        DB::transaction(function () use ($wave, $actor) {
            if ($wave->status === 'ditutup') {
                // "Membuka kembali gelombang yang sudah ditutup mensyaratkan jadwal tutup baru atau tanpa jadwal tutup" (BR-02)
                if ($wave->closes_at && $wave->closes_at->isPast()) {
                    throw ValidationException::withMessages([
                        'status' => 'Gelombang yang sudah ditutup memerlukan jadwal tutup baru sebelum dibuka kembali.',
                    ]);
                }
            }

            $this->assertNoOverlap($wave->academic_year_id, $wave->opens_at, $wave->closes_at, ignoreWaveId: $wave->id, forOpening: true);

            $from = $wave->status;
            $wave->update(['status' => 'dibuka']);
            $this->logStatusChange($wave, $from, 'dibuka', $actor);
        });
    }

    // FR-PPDB-02: tutup manual.
    public function closeManually(AdmissionWave $wave, User $actor): void
    {
        DB::transaction(function () use ($wave, $actor) {
            $from = $wave->status;
            $wave->update(['status' => 'ditutup']);
            $this->logStatusChange($wave, $from, 'ditutup', $actor);
        });
    }

    // FR-PPDB-03: dipanggil scheduler, changed_by null (sistem).
    public function autoTransition(AdmissionWave $wave, string $toStatus): void
    {
        DB::transaction(function () use ($wave, $toStatus) {
            $from = $wave->status;
            $wave->update(['status' => $toStatus]);
            $this->logStatusChange($wave, $from, $toStatus, null);
        });
    }

    // BR-14: tolak jadwal tumpang tindih pada tahun ajaran yang sama.
    private function assertNoOverlap(int $academicYearId, \DateTimeInterface|string $opensAt, \DateTimeInterface|string|null $closesAt, ?int $ignoreWaveId = null, bool $forOpening = false): void
    {
        $query = AdmissionWave::where('academic_year_id', $academicYearId)
            ->whereIn('status', $forOpening ? ['dibuka'] : ['dijadwalkan', 'dibuka'])
            ->when($ignoreWaveId, fn ($q) => $q->where('id', '!=', $ignoreWaveId))
            ->lockForUpdate();

        if ($forOpening) {
            // BR-14 inti: hanya satu gelombang dibuka pada satu waktu (constraint DB sudah menjamin,
            // ini pengecekan awal agar error-nya jelas, bukan cuma "duplicate entry" dari DB).
            if ($query->exists()) {
                throw ValidationException::withMessages([
                    'status' => 'Ada gelombang lain yang sedang dibuka. Tutup gelombang itu terlebih dahulu.',
                ]);
            }

            return;
        }

        $overlapping = $query->get()->contains(function (AdmissionWave $other) use ($opensAt, $closesAt) {
            $otherCloses = $other->closes_at ?? now()->addYears(100);
            $newCloses = $closesAt ?? now()->addYears(100);

            return $opensAt < $otherCloses && $other->opens_at < $newCloses;
        });

        if ($overlapping) {
            throw ValidationException::withMessages([
                'opens_at' => 'Jadwal ini tumpang tindih dengan gelombang lain pada tahun ajaran yang sama.',
            ]);
        }
    }

    private function logStatusChange(AdmissionWave $wave, ?string $from, string $to, ?User $actor): void
    {
        AuditLog::create([
            'user_id' => $actor?->id,
            'action' => 'admission_waves.status_changed',
            'auditable_type' => AdmissionWave::class,
            'auditable_id' => $wave->id,
            'old_values' => ['status' => $from],
            'new_values' => ['status' => $to],
            'ip_address' => request()->ip(),
        ]);
    }
}
