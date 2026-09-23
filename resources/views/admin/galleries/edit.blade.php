@extends('layouts.app')

@section('title', 'Kelola Album')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.galleries.index') }}" class="text-sm text-muted hover:text-text transition-colors">&larr; Galeri Foto</a>
        <h1 class="text-xl font-semibold text-text mt-2">Kelola Album — {{ $gallery->title }}</h1>
    </div>

    <div class="bg-surface border border-border rounded-lg p-6">
        <h2 class="text-sm font-semibold text-text mb-4">Detail Album</h2>
        <form method="POST" action="{{ route('admin.galleries.update', $gallery) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-text mb-1">Judul Album</label>
                <input id="title" type="text" name="title" value="{{ old('title', $gallery->title) }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
                       required>
                @error('title')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-text mb-1">Deskripsi</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">{{ old('description', $gallery->description) }}</textarea>
            </div>

            @if ($gallery->cover_image_path)
                <img src="{{ Storage::url($gallery->cover_image_path) }}" class="w-32 h-20 object-cover rounded-md border border-border">
            @endif
            <div>
                <label for="cover" class="block text-sm font-medium text-text mb-1">Ganti Foto Sampul <span class="text-muted font-normal">(opsional)</span></label>
                <input id="cover" type="file" name="cover" accept="image/*"
                       class="w-full text-sm text-text file:mr-3 file:rounded-md file:border-0 file:bg-background file:px-3 file:py-2 file:text-sm">
                @error('cover')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-text">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $gallery->is_published) ? 'checked' : '' }}
                       class="rounded border-border text-primary focus:ring-primary/40">
                Terbitkan
            </label>

            <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Simpan</button>
        </form>
    </div>

    <div class="bg-surface border border-border rounded-lg p-6">
        <h2 class="text-sm font-semibold text-text mb-4">Tambah Foto</h2>
        <form method="POST" action="{{ route('admin.galleries.photos.store', $gallery) }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <input type="file" name="photos[]" accept="image/*" multiple
                   class="w-full text-sm text-text file:mr-3 file:rounded-md file:border-0 file:bg-background file:px-3 file:py-2 file:text-sm">
            @error('photos')<p class="text-sm text-danger">{{ $message }}</p>@enderror
            @error('photos.*')<p class="text-sm text-danger">{{ $message }}</p>@enderror
            <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Unggah Foto</button>
        </form>

        @if ($gallery->photos->isNotEmpty())
            <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mt-6">
                @foreach ($gallery->photos as $photo)
                    <div class="relative group">
                        <img src="{{ Storage::url($photo->image_path) }}" class="w-full aspect-square object-cover rounded-md border border-border">
                        <form action="{{ route('admin.galleries.photos.destroy', $photo) }}" method="POST"
                              onsubmit="return confirm('Hapus foto ini?');"
                              class="absolute top-1 right-1">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-danger text-white text-xs rounded px-1.5 py-0.5">Hapus</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
