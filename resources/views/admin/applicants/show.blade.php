@extends('layouts.app')
@section('title', $applicant->full_name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.admission-waves.applicants', $applicant->admission_wave_id) }}" class="text-sm text-muted hover:text-text transition-colors">&larr; Daftar Pendaftar</a>
        <h1 class="text-xl font-semibold text-text mt-2">{{ $applicant->full_name }}</h1>
        <p class="text-sm text-muted mt-1">No. {{ $applicant->registration_number ?? '—' }} · Status: <strong>{{ str_replace('_', ' ', $applicant->status) }}</strong></p>
    </div>

    <div class="bg-surface border border-border rounded-lg p-6">
        <h2 class="text-sm font-semibold text-text mb-3">Biodata</h2>
        <dl class="grid grid-cols-2 gap-3 text-sm">
            <div><dt class="text-muted">Jenis</dt><dd class="text-text">{{ $applicant->type === 'baru' ? 'Siswa Baru' : 'Pindahan' }}</dd></div>
            <div><dt class="text-muted">Jenis Kelamin</dt><dd class="text-text">{{ $applicant->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd></div>
            <div><dt class="text-muted">Tempat, Tgl Lahir</dt><dd class="text-text">{{ $applicant->birth_place }}, {{ $applicant->birth_date->translatedFormat('d M Y') }}</dd></div>
            <div><dt class="text-muted">NISN</dt><dd class="text-text">{{ $applicant->nisn ?? '-' }}</dd></div>
            <div class="col-span-2"><dt class="text-muted">Alamat</dt><dd class="text-text">{{ $applicant->address }}</dd></div>
        </dl>
    </div>

    <div class="bg-surface border border-border rounded-lg p-6">
        <h2 class="text-sm font-semibold text-text mb-3">Orang Tua/Wali</h2>
        <div class="space-y-2 text-sm">
            @foreach ($applicant->guardians as $guardian)
                <p class="text-text">{{ ucfirst($guardian->relation) }}: {{ $guardian->full_name }} — {{ $guardian->phone ?? '-' }}</p>
            @endforeach
        </div>
    </div>

    <div class="bg-surface border border-border rounded-lg p-6">
        <h2 class="text-sm font-semibold text-text mb-3">Berkas</h2>
        <div class="space-y-2 text-sm">
            @forelse ($applicant->documents as $doc)
                <a href="{{ route('ppdb.applicants.documents.show', $doc) }}" target="_blank" class="block text-primary hover:text-primary-dark transition-colors">
                    {{ $doc->type }} — {{ $doc->original_name }}
                </a>
            @empty
                <p class="text-muted">Belum ada berkas diunggah.</p>
            @endforelse
        </div>
    </div>

    @can('verify', $applicant)
        @if ($applicant->status === 'dikirim')
            <div class="bg-surface border border-border rounded-lg p-6">
                <h2 class="text-sm font-semibold text-text mb-3">Verifikasi Berkas</h2>
                <form method="POST" action="{{ route('admin.applicants.verify', $applicant) }}" class="space-y-3">
                    @csrf
                    <select name="result" class="w-full rounded-md border border-border px-3 py-2 text-sm" required>
                        <option value="">Pilih hasil...</option>
                        <option value="terverifikasi">Lengkap dan Sesuai</option>
                        <option value="perlu_perbaikan">Perlu Diperbaiki</option>
                        <option value="ditolak">Tidak Memenuhi Syarat</option>
                    </select>
                    <textarea name="note" rows="2" placeholder="Catatan/alasan (wajib untuk perlu diperbaiki/ditolak)" class="w-full rounded-md border border-border px-3 py-2 text-sm"></textarea>
                    @error('review_note')<p class="text-sm text-danger">{{ $message }}</p>@enderror
                    <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Simpan Verifikasi</button>
                </form>
            </div>
        @endif
    @endcan

    @can('decide', $applicant)
        @if ($applicant->status === 'terverifikasi')
            <div class="bg-surface border border-border rounded-lg p-6">
                <h2 class="text-sm font-semibold text-text mb-3">Tetapkan Hasil Seleksi</h2>
                <div class="flex gap-3">
                    <form method="POST" action="{{ route('admin.applicants.decide', $applicant) }}">
                        @csrf<input type="hidden" name="result" value="diterima">
                        <button type="submit" class="rounded-md bg-success px-4 py-2 text-sm font-medium text-white hover:bg-success/90 transition-colors">Diterima</button>
                    </form>
                    <form method="POST" action="{{ route('admin.applicants.decide', $applicant) }}">
                        @csrf<input type="hidden" name="result" value="tidak_diterima">
                        <button type="submit" class="rounded-md bg-danger px-4 py-2 text-sm font-medium text-white hover:bg-danger/90 transition-colors">Tidak Diterima</button>
                    </form>
                </div>
            </div>
        @elseif ($applicant->wave?->results_announced_at && in_array($applicant->status, ['diterima', 'tidak_diterima']))
            <div class="bg-surface border border-border rounded-lg p-6">
                <h2 class="text-sm font-semibold text-text mb-3">Revisi Hasil (Setelah Diumumkan)</h2>
                <form method="POST" action="{{ route('admin.applicants.revise-decision', $applicant) }}" class="space-y-3"
                      onsubmit="return confirm('Revisi hasil seleksi ini? Wajib diisi alasan dan akan tercatat di audit log.');">
                    @csrf
                    <select name="result" class="w-full rounded-md border border-border px-3 py-2 text-sm" required>
                        <option value="diterima" {{ $applicant->status === 'diterima' ? 'disabled' : '' }}>Diterima</option>
                        <option value="tidak_diterima" {{ $applicant->status === 'tidak_diterima' ? 'disabled' : '' }}>Tidak Diterima</option>
                    </select>
                    <textarea name="reason" rows="2" placeholder="Alasan revisi (wajib)" class="w-full rounded-md border border-border px-3 py-2 text-sm" required></textarea>
                    <button type="submit" class="rounded-md bg-warning px-4 py-2 text-sm font-medium text-white hover:bg-warning/90 transition-colors">Simpan Revisi</button>
                </form>
            </div>
        @endif
    @endcan

    @can('recordReenrollment', $applicant)
        @if ($applicant->status === 'diterima')
            <div class="bg-surface border border-border rounded-lg p-6">
                <h2 class="text-sm font-semibold text-text mb-3">Daftar Ulang</h2>
                <div class="flex gap-3">
                    <form method="POST" action="{{ route('admin.applicants.reenrollment', $applicant) }}">
                        @csrf<input type="hidden" name="result" value="daftar_ulang">
                        <button type="submit" class="rounded-md bg-success px-4 py-2 text-sm font-medium text-white hover:bg-success/90 transition-colors">Catat Daftar Ulang</button>
                    </form>
                    <form method="POST" action="{{ route('admin.applicants.reenrollment', $applicant) }}">
                        @csrf<input type="hidden" name="result" value="mengundurkan_diri">
                        <button type="submit" class="rounded-md bg-danger px-4 py-2 text-sm font-medium text-white hover:bg-danger/90 transition-colors">Mengundurkan Diri</button>
                    </form>
                </div>
            </div>
        @endif

        @can('convert', $applicant)
        @if ($applicant->status === 'daftar_ulang')
            <div class="bg-surface border border-border rounded-lg p-6">
                <h2 class="text-sm font-semibold text-text mb-3">Konversi ke Data Siswa</h2>
                <form method="POST" action="{{ route('admin.applicants.convert', $applicant) }}" onsubmit="return confirm('Konversi pendaftar ini menjadi data siswa resmi? Tindakan ini hanya bisa dilakukan sekali.');">
                    @csrf
                    <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Konversi Sekarang</button>
                </form>
            </div>
        @endif
    @endcan
    @endcan

    <div class="bg-surface border border-border rounded-lg p-6">
        <h2 class="text-sm font-semibold text-text mb-3">Riwayat Status</h2>
        <div class="space-y-2 text-sm">
            @foreach ($applicant->statusHistories as $history)
                <p class="text-muted">
                    {{ $history->created_at->translatedFormat('d M Y, H:i') }} —
                    {{ $history->from_status ?? 'awal' }} &rarr; {{ $history->to_status }}
                    @if ($history->note) ({{ $history->note }}) @endif
                    @if ($history->changer) oleh {{ $history->changer->name }} @endif
                </p>
            @endforeach
        </div>
    </div>
</div>
@endsection
