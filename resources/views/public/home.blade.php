@extends('layouts.public')

@section('content')
@if ($banners->isNotEmpty())
    <div class="mb-8 rounded-lg overflow-hidden border border-border">
        <img src="{{ Storage::url($banners->first()->image_path) }}" class="w-full aspect-3/1 object-cover">
    </div>
@endif

<section class="mb-10">
    <h2 class="text-lg font-semibold text-text mb-4">Berita & Pengumuman Terbaru</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @forelse ($posts as $post)
            <a href="{{ route('posts.show', $post->slug) }}" class="block bg-surface border border-border rounded-lg p-4 hover:border-primary transition-colors">
                <p class="font-medium text-text">{{ $post->title }}</p>
                <p class="text-sm text-muted mt-1">{{ $post->published_at->translatedFormat('d M Y') }}</p>
            </a>
        @empty
            <p class="text-sm text-muted">Belum ada berita.</p>
        @endforelse
    </div>
</section>

<section>
    <h2 class="text-lg font-semibold text-text mb-4">Agenda Mendatang</h2>
    <div class="space-y-3">
        @forelse ($events as $event)
            <div class="bg-surface border border-border rounded-lg p-4">
                <p class="font-medium text-text">{{ $event->title }}</p>
                <p class="text-sm text-muted mt-1">{{ $event->starts_at->translatedFormat('d M Y, H:i') }} @if($event->location) · {{ $event->location }} @endif</p>
            </div>
        @empty
            <p class="text-sm text-muted">Belum ada agenda mendatang.</p>
        @endforelse
    </div>
</section>
@endsection
