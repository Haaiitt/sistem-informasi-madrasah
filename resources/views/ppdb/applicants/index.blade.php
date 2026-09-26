@extends('layouts.app')
@section('title', 'Pendaftaran Anak')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-text">Pendaftaran Anak</h1>
        <a href="{{ route('ppdb.applicants.create') }}" class="inline-flex items-center gap-1.5 rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
            + Tambah Pendaftaran
        </a>
    </div>

    @if ($applicants->isEmpty())
        <div class="text-center border border-dashed border-border rounded-lg py-16 px-4">
            <p class="font-medium text-text">Belum Ada Pendaftaran</p>
            <p class="text-sm text-muted mt-1">Klik "Tambah Pendaftaran" untuk mendaftarkan anak.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($applicants as $applicant)
                <a href="{{ route('ppdb.applicants.edit', $applicant) }}" class="block bg-surface border border-border rounded-lg p-4 hover:border-primary transition-colors">
                    <div class="flex items-center justify-between">
                        <p class="font-medium text-text">{{ $applicant->full_name }}</p>
                        <span class="text-xs font-medium text-muted uppercase">{{ str_replace('_', ' ', $applicant->status) }}</span>
                    </div>
                    <p class="text-sm text-muted mt-1">{{ $applicant->type === 'baru' ? 'Siswa Baru' : 'Pindahan' }}
                        @if ($applicant->registration_number) · No. {{ $applicant->registration_number }} @endif</p>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
