<?php

namespace App\Services;

use App\Models\AdmissionWave;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Mews\Purifier\Facades\Purifier;

class AnnouncementService
{
    // FR-MSG-02 / DB-05: penerima ditetapkan SAAT PENGIRIMAN, snapshot, bukan dinamis.
    public function send(?AdmissionWave $wave, string $title, string $body, User $actor): Announcement
    {
        return DB::transaction(function () use ($wave, $title, $body, $actor) {
            $recipientIds = $this->resolveRecipients($wave);

            $announcement = Announcement::create([
                'admission_wave_id' => $wave?->id,
                'created_by' => $actor->id,
                'title' => $title,
                'body' => Purifier::clean($body, 'text'),
                'recipient_count' => count($recipientIds),
            ]);

            $announcement->recipients()->attach($recipientIds);

            // BR-22: pengiriman pengumuman massal wajib tercatat di audit log.
            AuditLog::create([
                'user_id' => $actor->id, 'action' => 'announcements.sent',
                'auditable_type' => Announcement::class, 'auditable_id' => $announcement->id,
                'new_values' => ['recipient_count' => count($recipientIds), 'wave_id' => $wave?->id],
                'ip_address' => request()->ip(),
            ]);

            return $announcement;
        });
    }

    public function markRead(Announcement $announcement, User $user): void
    {
        $announcement->recipients()->updateExistingPivot($user->id, ['read_at' => now()]);
    }

    private function resolveRecipients(?AdmissionWave $wave): array
    {
        if (! $wave) {
            // Semua peserta = akun ber-role calon_siswa.
            return Role::where('name', 'calon_siswa')->first()->users()->pluck('users.id')->all();
        }

        // Per gelombang: pemilik akun yang punya pendaftaran berstatus sudah dikirim pada gelombang itu.
        return $wave->applicants()
            ->whereIn('status', ['dikirim', 'perlu_perbaikan', 'terverifikasi', 'diterima', 'ditolak', 'tidak_diterima', 'daftar_ulang', 'mengundurkan_diri', 'menjadi_siswa'])
            ->pluck('user_id')
            ->unique()
            ->values()
            ->all();
    }
}
