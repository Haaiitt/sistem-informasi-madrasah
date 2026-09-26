<?php

namespace App\Services;

use App\Models\AdmissionWave;
use App\Models\Applicant;
use App\Models\ApplicantStatusHistory;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApplicantReviewService
{
    public function __construct(private ApplicantStatusMachine $statusMachine)
    {
    }

    // FR-PPDB-11: verifikasi berkas.
    public function verify(Applicant $applicant, string $result, ?string $note, User $actor): void
    {
        if (! in_array($result, ['terverifikasi', 'perlu_perbaikan', 'ditolak'])) {
            throw ValidationException::withMessages(['result' => 'Hasil verifikasi tidak valid.']);
        }

        if (! $this->statusMachine->canTransition($applicant->status, $result)) {
            throw ValidationException::withMessages(['status' => 'Pendaftaran ini tidak dapat diverifikasi dari status saat ini.']);
        }

        // UF-07: catatan wajib untuk perlu_perbaikan, alasan wajib untuk ditolak.
        if (in_array($result, ['perlu_perbaikan', 'ditolak']) && ! $note) {
            throw ValidationException::withMessages(['review_note' => 'Catatan/alasan wajib diisi.']);
        }

        $this->transition($applicant, $result, $note, $actor);
    }

    // FR-PPDB-12: tetapkan hasil seleksi.
    public function decide(Applicant $applicant, string $result, User $actor): void
    {
        if (! in_array($result, ['diterima', 'tidak_diterima'])) {
            throw ValidationException::withMessages(['result' => 'Hasil seleksi tidak valid.']);
        }

        if (! $this->statusMachine->canTransition($applicant->status, $result)) {
            throw ValidationException::withMessages(['status' => 'Hasil seleksi hanya dapat ditetapkan untuk pendaftaran terverifikasi.']);
        }

        $this->transition($applicant, $result, null, $actor);
    }

    // BR-13: ubah hasil SETELAH diumumkan — kasus khusus di luar diagram normal, wajib alasan, tercatat di audit_logs.
    public function reviseDecisionAfterAnnouncement(Applicant $applicant, string $newResult, string $reason, User $actor): void
    {
        if (! in_array($applicant->status, ['diterima', 'tidak_diterima']) || ! in_array($newResult, ['diterima', 'tidak_diterima'])) {
            throw ValidationException::withMessages(['result' => 'Revisi hasil hanya berlaku antara diterima/tidak diterima.']);
        }

        if (! $applicant->wave || ! $applicant->wave->results_announced_at) {
            throw ValidationException::withMessages(['status' => 'Revisi hanya berlaku setelah hasil gelombang diumumkan.']);
        }

        DB::transaction(function () use ($applicant, $newResult, $reason, $actor) {
            $from = $applicant->status;
            $applicant->update(['status' => $newResult]);

            ApplicantStatusHistory::create([
                'applicant_id' => $applicant->id, 'from_status' => $from, 'to_status' => $newResult,
                'note' => $reason, 'changed_by' => $actor->id,
            ]);

            AuditLog::create([
                'user_id' => $actor->id, 'action' => 'applicants.decision_revised',
                'auditable_type' => Applicant::class, 'auditable_id' => $applicant->id,
                'old_values' => ['status' => $from], 'new_values' => ['status' => $newResult, 'reason' => $reason],
                'ip_address' => request()->ip(),
            ]);
        });
    }

    // FR-PPDB-13: umumkan hasil per gelombang. Tidak memblokir walau masih ada yang belum ditetapkan (UF-07).
    public function announceResults(AdmissionWave $wave): int
    {
        $pending = Applicant::where('admission_wave_id', $wave->id)
            ->whereIn('status', ['dikirim', 'terverifikasi'])
            ->count();

        $wave->update(['results_announced_at' => now()]);

        return $pending; // dipakai Controller untuk pesan peringatan, bukan pemblokir.
    }

    // FR-PPDB-15: catat daftar ulang / pengunduran diri.
    public function recordReenrollment(Applicant $applicant, string $result, User $actor): void
    {
        if (! in_array($result, ['daftar_ulang', 'mengundurkan_diri'])) {
            throw ValidationException::withMessages(['result' => 'Status tidak valid.']);
        }

        if (! $this->statusMachine->canTransition($applicant->status, $result)) {
            throw ValidationException::withMessages(['status' => 'Hanya pendaftar diterima yang dapat dicatat daftar ulang/mengundurkan diri.']);
        }

        $this->transition($applicant, $result, null, $actor);
    }

    private function transition(Applicant $applicant, string $to, ?string $note, User $actor): void
    {
        DB::transaction(function () use ($applicant, $to, $note, $actor) {
            $from = $applicant->status;

            $applicant->update([
                'status' => $to,
                'review_note' => $note,
                'reviewed_by' => $actor->id,
                'reviewed_at' => now(),
            ]);

            ApplicantStatusHistory::create([
                'applicant_id' => $applicant->id, 'from_status' => $from, 'to_status' => $to,
                'note' => $note, 'changed_by' => $actor->id,
            ]);

            // BR-12: perubahan status pendaftaran wajib masuk audit log.
            AuditLog::create([
                'user_id' => $actor->id, 'action' => 'applicants.status_changed',
                'auditable_type' => Applicant::class, 'auditable_id' => $applicant->id,
                'old_values' => ['status' => $from], 'new_values' => ['status' => $to],
                'ip_address' => request()->ip(),
            ]);
        });
    }
}
