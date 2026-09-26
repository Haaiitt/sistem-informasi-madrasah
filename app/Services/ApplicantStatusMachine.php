<?php

namespace App\Services;

class ApplicantStatusMachine
{
    // Persis diagram UF-06. Perpindahan di luar ini DITOLAK.
    private const TRANSITIONS = [
        'draft' => ['dikirim'],
        'dikirim' => ['terverifikasi', 'perlu_perbaikan', 'ditolak'],
        'perlu_perbaikan' => ['dikirim'],
        'terverifikasi' => ['diterima', 'tidak_diterima'],
        'diterima' => ['daftar_ulang', 'mengundurkan_diri'],
        'daftar_ulang' => ['menjadi_siswa'],
    ];

    public function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? []);
    }

    public function isFinal(string $status): bool
    {
        return ! array_key_exists($status, self::TRANSITIONS);
    }
}
