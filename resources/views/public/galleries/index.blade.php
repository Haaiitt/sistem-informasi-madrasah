@extends('layouts.public')
@section('title', 'Galeri Foto')

@section('content')
<h1 class="text-xl font-semibold text-text mb-6">Galeri Foto</h1>
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
    @forelse ($galleries as $gallery)
        <a href="{{ route('galleries.show', $gallery->id) }}" class="block bg-surface border border-border rounded-lg overflow-hidden hover:border-primary transition-colors">
            <div class="aspect-video bg-background">
                @if ($gallery->cover_image_path)
                    <img src="{{ Storage::url($gallery->cover_image_path) }}" class="w-full h-full object-cover">
                @endif
            </div>
            <div class="p-3">
                <p class="font-medium text-text text-sm truncate">{{ $gallery->title }}</p>
                <p class="text-xs text-muted mt-0.5">{{ $gallery->photos_count }} foto</p>
            </div>
        </a>
    @empty
        <p class="text-sm text-muted col-span-full">Belum ada album foto.</p>
    @endforelse
</div>
<div class="mt-6">{{ $galleries->links() }}</div>
@endsection
