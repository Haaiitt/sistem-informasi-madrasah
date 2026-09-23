{{-- resources/views/admin/banners/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Tambah Banner')
@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.banners.index') }}" class="text-sm text-muted hover:text-text transition-colors">&larr; Banner Beranda</a>
        <h1 class="text-xl font-semibold text-text mt-2">Tambah Banner</h1>
    </div>
    <div class="bg-surface border border-border rounded-lg p-6">
        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @include('admin.banners.partials.form-fields')
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Simpan</button>
                <a href="{{ route('admin.banners.index') }}" class="rounded-md border border-border px-4 py-2 text-sm font-medium text-secondary hover:bg-background transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
