@extends('layouts.public')
@section('title', $post->title)

@section('content')
<article class="max-w-2xl mx-auto">
    <a href="{{ route('posts.index') }}" class="text-sm text-muted hover:text-text transition-colors">&larr; Berita & Pengumuman</a>
    <h1 class="text-2xl font-semibold text-text mt-3">{{ $post->title }}</h1>
    <p class="text-sm text-muted mt-2">{{ $post->category?->name ?? 'Umum' }} · {{ $post->published_at->translatedFormat('d M Y') }} · {{ $post->author->name }}</p>
    <div class="prose prose-sm max-w-none mt-6 text-text">{!! $post->body !!}</div>
</article>
@endsection
