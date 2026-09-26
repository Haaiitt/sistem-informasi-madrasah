<?php

namespace App\Console\Commands;

use App\Models\AdmissionWave;
use App\Services\AdmissionWaveService;
use Illuminate\Console\Command;

class TransitionAdmissionWaves extends Command
{
    protected $signature = 'admission-waves:transition';
    protected $description = 'Pindahkan status gelombang PPDB sesuai jadwal (FR-PPDB-03, BR-02)';

    public function handle(AdmissionWaveService $service): void
    {
        // dijadwalkan -> dibuka, saat opens_at tercapai.
        AdmissionWave::where('status', 'dijadwalkan')
            ->where('opens_at', '<=', now())
            ->each(function (AdmissionWave $wave) use ($service) {
                $service->autoTransition($wave, 'dibuka');
                $this->info("Gelombang #{$wave->id} dibuka otomatis.");
            });

        // dibuka -> ditutup, saat closes_at tercapai.
        AdmissionWave::where('status', 'dibuka')
            ->whereNotNull('closes_at')
            ->where('closes_at', '<=', now())
            ->each(function (AdmissionWave $wave) use ($service) {
                $service->autoTransition($wave, 'ditutup');
                $this->info("Gelombang #{$wave->id} ditutup otomatis.");
            });
    }
}
