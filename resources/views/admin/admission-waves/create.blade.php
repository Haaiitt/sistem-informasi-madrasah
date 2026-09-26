{{-- resources/views/admin/admission-waves/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Gelombang Baru')
@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.admission-waves.index') }}" class="text-sm text-muted hover:text-text transition-colors">&larr; Gelombang PPDB</a>
        <h1 class="text-xl font-semibold text-text mt-2">Gelombang Baru</h1>
    </div>
    <div class="bg-surface border border-border rounded-lg p-6">
        <form method="POST" action="{{ route('admin.admission-waves.store') }}" class="space-y-5">
            @csrf
            @include('admin.admission-waves.partials.form-fields', ['academicYears' => $academicYears])
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Simpan</button>
                <a href="{{ route('admin.admission-waves.index') }}" class="rounded-md border border-border px-4 py-2 text-sm font-medium text-secondary hover:bg-background transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
