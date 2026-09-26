@extends('layouts.app')
@section('title', 'Gelombang PPDB')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-text">Gelombang PPDB</h1>
            <p class="text-sm text-muted mt-1">Kelola jadwal penerimaan peserta didik baru.</p>
        </div>
        @can('create', App\Models\AdmissionWave::class)
        <a href="{{ route('admin.admission-waves.create') }}"
           class="inline-flex items-center gap-1.5 rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
            + Gelombang Baru
        </a>
        @endcan
    </div>

    @if ($waves->isEmpty())
        <div class="text-center border border-dashed border-border rounded-lg py-16 px-4">
            <p class="font-medium text-text">Belum Ada Gelombang</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($waves as $wave)
                <div class="bg-surface border border-border rounded-lg p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-medium text-text">{{ $wave->name }}</p>
                            <p class="text-sm text-muted mt-1">{{ $wave->academicYear->name }} · {{ $wave->opens_at->translatedFormat('d M Y, H:i') }}
                                — {{ $wave->closes_at?->translatedFormat('d M Y, H:i') ?? 'tanpa batas tutup' }}</p>
                        </div>
                        @php
                            $labels = ['dijadwalkan' => ['Dijadwalkan', 'muted', '○'], 'dibuka' => ['Dibuka', 'success', '●'], 'ditutup' => ['Ditutup', 'warning', '◐']];
                            [$label, $tone, $symbol] = $labels[$wave->status] ?? ['—', 'muted', '○'];
                        @endphp
                        <span class="shrink-0 inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium
                                     {{ $tone === 'success' ? 'bg-success-bg text-success' : ($tone === 'warning' ? 'bg-warning-bg text-warning' : 'bg-background text-muted') }}">
                            {{ $symbol }} {{ $label }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4 mt-3 pt-3 border-t border-border">
                        @can('update', $wave)
                            <a href="{{ route('admin.admission-waves.edit', $wave) }}" class="text-sm font-medium text-secondary hover:text-primary transition-colors">Ubah Jadwal</a>
                        @endcan
                        @if ($wave->status !== 'dibuka')
                            @can('open', $wave)
                                <form action="{{ route('admin.admission-waves.open', $wave) }}" method="POST" onsubmit="return confirm('Buka gelombang ini sekarang?');">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-sm font-medium text-success hover:text-success/80 transition-colors">Buka Sekarang</button>
                                </form>
                            @endcan
                        @else
                            @can('close', $wave)
                                <form action="{{ route('admin.admission-waves.close', $wave) }}" method="POST" onsubmit="return confirm('Tutup gelombang ini sekarang?');">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-sm font-medium text-danger hover:text-danger/80 transition-colors">Tutup Sekarang</button>
                                </form>
                            @endcan
                        @endif
                        @can('viewAsStaff', App\Models\Applicant::class)
                            <a href="{{ route('admin.admission-waves.applicants', $wave) }}" class="text-sm font-medium text-secondary hover:text-primary transition-colors">Lihat Pendaftar</a>
                        @endcan
                        @can('publishResults', $wave)
                            @if (! $wave->results_announced_at)
                                <form action="{{ route('admin.admission-waves.announce', $wave) }}" method="POST" onsubmit="return confirm('Umumkan hasil gelombang ini? Orang tua akan dapat melihat hasil seleksi.');">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-sm font-medium text-primary hover:text-primary-dark transition-colors">Umumkan Hasil</button>
                                </form>
                            @else
                                <span class="text-sm text-muted">Diumumkan {{ $wave->results_announced_at->translatedFormat('d M Y') }}</span>
                            @endif
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $waves->links() }}</div>
    @endif
</div>
@endsection
