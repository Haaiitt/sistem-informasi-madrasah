@extends('layouts.app')

@section('title', 'Album Baru')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.galleries.index') }}" class="text-sm text-muted hover:text-text transition-colors">&larr; Galeri Foto</a>
        <h1 class="text-xl font-semibold text-text mt-2">Album Baru</h1>
    </div>

    <div class="bg-surface border border-border rounded-lg p-6">
        <form method="POST" action="{{ route('admin.galleries.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-text mb-1">Judul Album</label>
                <input id="title" type="text" name="title" value="{{ old('title') }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
                       required>
                @error('title')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-text mb-1">Deskripsi <span class="text-muted font-normal">(opsional)</span></label>
                <textarea id="description" name="description" rows="3"
                          class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">{{ old('description') }}</textarea>
                @error('description')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="cover" class="block text-sm font-medium text-text mb-1">Foto Sampul <span class="text-muted font-normal">(opsional)</span></label>
                <input id="cover" type="file" name="cover" accept="image/*"
                       class="w-full text-sm text-text file:mr-3 file:rounded-md file:border-0 file:bg-background file:px-3 file:py-2 file:text-sm">
                @error('cover')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-text">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                       class="rounded border-border text-primary focus:ring-primary/40">
                Terbitkan sekarang
            </label>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Simpan & Lanjut Tambah Foto</button>
                <a href="{{ route('admin.galleries.index') }}" class="rounded-md border border-border px-4 py-2 text-sm font-medium text-secondary hover:bg-background transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
