{{-- resources/views/ppdb/applicants/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Tambah Pendaftaran')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('ppdb.applicants.index') }}" class="text-sm text-muted hover:text-text transition-colors">&larr; Pendaftaran Anak</a>
        <h1 class="text-xl font-semibold text-text mt-2">Tambah Pendaftaran</h1>
    </div>
    @if (! $waveOpen)
        <div class="bg-warning-bg border border-warning/20 text-warning text-sm rounded-md p-3 mb-4">
            Tidak ada gelombang PPDB yang sedang dibuka. Draft tidak dapat disimpan saat ini.
        </div>
    @endif
    <div class="bg-surface border border-border rounded-lg p-6">
        <form method="POST" action="{{ route('ppdb.applicants.store') }}" class="space-y-4">
            @csrf
            @include('ppdb.applicants.partials.form-fields')
            <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors" {{ $waveOpen ? '' : 'disabled' }}>
                Simpan Draft
            </button>
        </form>
    </div>
</div>
@endsection
