<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\PasswordResetLink;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasswordResetLinkService
{
    // architecture.md §5: token 32-byte acak, disimpan sebagai hash SHA-256, berlaku 24 jam.
    public function issue(User $target, User $issuedBy): string
    {
        $rawToken = bin2hex(random_bytes(32));

        DB::transaction(function () use ($target, $issuedBy, $rawToken) {
            // Hanya satu tautan aktif berlaku; batalkan yang lama.
            PasswordResetLink::where('user_id', $target->id)
                ->whereNull('used_at')
                ->whereNull('revoked_at')
                ->update(['revoked_at' => now()]);

            PasswordResetLink::create([
                'user_id' => $target->id,
                'issued_by' => $issuedBy->id,
                'token_hash' => hash('sha256', $rawToken),
                'expires_at' => now()->addHours(24),
            ]);

            // BR-20 & database.md DB-04: penerbitan tautan wajib tercatat di audit log.
            AuditLog::create([
                'user_id' => $issuedBy->id,
                'action' => 'users.password_reset_link_issued',
                'auditable_type' => User::class,
                'auditable_id' => $target->id,
                'ip_address' => request()->ip(),
            ]);
        });

        return $rawToken;
    }

    public function findValidLink(string $rawToken): ?PasswordResetLink
    {
        return PasswordResetLink::query()
            ->where('token_hash', hash('sha256', $rawToken))
            ->whereNull('used_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();
    }

    public function consume(PasswordResetLink $link, string $newPassword): void
    {
        DB::transaction(function () use ($link, $newPassword) {
            $link->user->update(['password' => Hash::make($newPassword)]);
            $link->update(['used_at' => now()]);

            // UF-02 langkah terakhir: akhiri sesi lama.
            DB::table('sessions')->where('user_id', $link->user_id)->delete();
        });
    }
}
