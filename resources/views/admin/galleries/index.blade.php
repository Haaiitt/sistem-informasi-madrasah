@extends('layouts.app')

@section('title', 'Galeri Foto')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-text">Galeri Foto</h1>
            <p class="text-sm text-muted mt-1">Kelola album dan foto kegiatan madrasah.</p>
        </div>
        @can('create', App\Models\Gallery::class)
        <a href="{{ route('admin.galleries.create') }}"
           class="inline-flex items-center gap-1.5 rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
            + Album Baru
        </a>
        @endcan
    </div>

    @if ($galleries->isEmpty())
        <div class="text-center border border-dashed border-border rounded-lg py-16 px-4">
            <p class="font-medium text-text">Belum Ada Album</p>
            <p class="text-sm text-muted mt-1">Belum ada album foto yang dibuat.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($galleries as $gallery)
                <div class="bg-surface border border-border rounded-lg overflow-hidden">
                    <div class="aspect-video bg-background flex items-center justify-center">
                        @if ($gallery->cover_image_path)
                            <img src="{{ Storage::url($gallery->cover_image_path) }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-muted text-sm">Tanpa sampul</span>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <p class="font-medium text-text">{{ $gallery->title }}</p>
                            @if ($gallery->is_published)
                                <span class="shrink-0 inline-flex items-center gap-1 rounded-full bg-success-bg px-2 py-0.5 text-xs font-medium text-success">● Terbit</span>
                            @else
                                <span class="shrink-0 inline-flex items-center gap-1 rounded-full bg-background px-2 py-0.5 text-xs font-medium text-muted">○ Draft</span>
                            @endif
                        </div>
                        <p class="text-sm text-muted mt-1">{{ $gallery->photos_count }} foto</p>
                        <div class="flex items-center gap-4 mt-3 pt-3 border-t border-border">
                            @can('update', $gallery)
                                <a href="{{ route('admin.galleries.edit', $gallery) }}" class="text-sm font-medium text-secondary hover:text-primary transition-colors">Kelola</a>
                            @endcan
                            @can('delete', $gallery)
                                <form action="{{ route('admin.galleries.destroy', $gallery) }}" method="POST"
                                      onsubmit="return confirm('Hapus album \'{{ $gallery->title }}\' beserta semua fotonya?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-danger hover:text-danger/80 transition-colors">Hapus</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $galleries->links() }}</div>
    @endif
</div>
@endsection
