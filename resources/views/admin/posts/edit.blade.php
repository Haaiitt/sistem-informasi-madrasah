@extends('layouts.app')

@section('title', 'Ubah Konten')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.posts.index') }}" class="text-sm text-muted hover:text-text transition-colors">&larr; Berita & Pengumuman</a>
        <h1 class="text-xl font-semibold text-text mt-2">Ubah Konten</h1>
    </div>

    <div class="bg-surface border border-border rounded-lg p-6">
        <form method="POST" action="{{ route('admin.posts.update', $post) }}" class="space-y-5">
            @csrf @method('PUT')
            @include('admin.posts.partials.form-fields', ['categories' => $categories, 'post' => $post])

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Simpan</button>
                <a href="{{ route('admin.posts.index') }}" class="rounded-md border border-border px-4 py-2 text-sm font-medium text-secondary hover:bg-background transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
