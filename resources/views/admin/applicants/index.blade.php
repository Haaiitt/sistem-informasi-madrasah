@extends('layouts.app')
@section('title', 'Pendaftar — ' . $wave->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('admin.admission-waves.index') }}" class="text-sm text-muted hover:text-text transition-colors">&larr; Gelombang PPDB</a>
    <h1 class="text-xl font-semibold text-text mt-2">Pendaftar — {{ $wave->name }}</h1>

    @can('export', App\Models\Applicant::class)
    <a href="{{ route('admin.admission-waves.applicants.export', $wave) }}" class="text-sm font-medium text-primary hover:text-primary-dark transition-colors">
        Ekspor Excel
    </a>
    @endcan

    @if ($applicants->isEmpty())
        <div class="text-center border border-dashed border-border rounded-lg py-16 px-4 mt-6">
            <p class="font-medium text-text">Belum ada pendaftar.</p>
        </div>
    @else
        <div class="mt-6 bg-surface border border-border rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-background text-left text-xs font-medium text-muted uppercase tracking-wide">
                        <th class="px-4 py-3">No. Registrasi</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($applicants as $applicant)
                        <tr class="hover:bg-background/60 transition-colors">
                            <td class="px-4 py-3 text-muted">{{ $applicant->registration_number ?? '—' }}</td>
                            <td class="px-4 py-3 font-medium text-text">{{ $applicant->full_name }}</td>
                            <td class="px-4 py-3 text-muted">{{ $applicant->type === 'baru' ? 'Baru' : 'Pindahan' }}</td>
                            <td class="px-4 py-3 text-muted text-xs uppercase">{{ str_replace('_', ' ', $applicant->status) }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.applicants.show', $applicant) }}" class="text-sm font-medium text-secondary hover:text-primary transition-colors">Periksa</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $applicants->links() }}</div>
    @endif
</div>
@endsection
