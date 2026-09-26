{{-- resources/views/ppdb/applicants/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Ubah Pendaftaran')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('ppdb.applicants.index') }}" class="text-sm text-muted hover:text-text transition-colors">&larr; Pendaftaran Anak</a>
        <h1 class="text-xl font-semibold text-text mt-2">{{ $applicant->full_name }}</h1>
        <p class="text-sm text-muted mt-1">Status: {{ str_replace('_', ' ', $applicant->status) }}</p>
    </div>

    @if ($applicant->isEditableByGuardian())
        <div class="bg-surface border border-border rounded-lg p-6">
            <form method="POST" action="{{ route('ppdb.applicants.update', $applicant) }}" class="space-y-4">
                @csrf @method('PUT')
                @include('ppdb.applicants.partials.form-fields', ['applicant' => $applicant])
                <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Simpan Draft</button>
            </form>
        </div>

        <div class="bg-surface border border-border rounded-lg p-6 mt-6">
            <h2 class="text-sm font-semibold text-text mb-4">Berkas Persyaratan</h2>
            @php
                $labels = ['kk' => 'Kartu Keluarga', 'akta_kelahiran' => 'Akta Kelahiran', 'pas_foto' => 'Pas Foto', 'keterangan_tk_ra' => 'Keterangan TK/RA', 'surat_pindah' => 'Surat Pindah'];
                $allowedTypes = app(App\Services\ApplicantDocumentService::class)->allowedTypes($applicant);
                $existingDocs = $applicant->documents->keyBy('type');
            @endphp
            <div class="space-y-3">
                @foreach ($allowedTypes as $type)
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span class="text-text">{{ $labels[$type] }}</span>
                        @if ($existingDocs->has($type))
                            <a href="{{ route('ppdb.applicants.documents.show', $existingDocs[$type]) }}" target="_blank" class="text-success">✓ Sudah diunggah</a>
                        @else
                            <span class="text-muted">Belum diunggah</span>
                        @endif
                        <form action="{{ route('ppdb.applicants.documents.store', $applicant) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">
                            <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf" class="text-xs">
                            <button type="submit" class="text-xs font-medium text-primary hover:text-primary-dark transition-colors">Unggah</button>
                        </form>
                    </div>
                @endforeach
            </div>
            @error('documents')<p class="mt-3 text-sm text-danger">{{ $message }}</p>@enderror
        </div>

        <form method="POST" action="{{ route('ppdb.applicants.submit', $applicant) }}" class="mt-6"
            onsubmit="return confirm('Kirim pendaftaran ini? Setelah dikirim, data tidak dapat diubah.');">
            @csrf
            <button type="submit" class="w-full rounded-md bg-success px-4 py-2 text-sm font-medium text-white hover:bg-success/90 transition-colors">
                Kirim Pendaftaran
            </button>
        </form>
        @error('status')<p class="mt-2 text-sm text-danger text-center">{{ $message }}</p>@enderror
    @else
        <div class="bg-surface border border-border rounded-lg p-6 text-sm text-secondary">
            Pendaftaran ini sudah terkunci (status: {{ str_replace('_', ' ', $applicant->status) }}) dan tidak dapat diubah lagi.
            @if ($applicant->registration_number)
                <a href="{{ route('ppdb.applicants.receipt', $applicant) }}" target="_blank" class="block mt-2 text-primary hover:text-primary-dark transition-colors">Lihat Bukti Pendaftaran</a>
            @endif
        </div>
    @endif
</div>
@endsection
