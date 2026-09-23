@extends('layouts.public')
@section('title', 'Berita & Pengumuman')

@section('content')
<h1 class="text-xl font-semibold text-text mb-6">Berita & Pengumuman</h1>

<form method="GET" class="flex flex-col sm:flex-row gap-3 mb-6">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari berita..."
           class="flex-1 rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
    <select name="kategori" class="rounded-md border border-border px-3 py-2 text-sm">
        <option value="">Semua Kategori</option>
        @foreach ($categories as $category)
            <option value="{{ $category->slug }}" {{ request('kategori') === $category->slug ? 'selected' : '' }}>{{ $category->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Cari</button>
</form>

<div class="space-y-4">
    @forelse ($posts as $post)
        <a href="{{ route('posts.show', $post->slug) }}" class="block bg-surface border border-border rounded-lg p-4 hover:border-primary transition-colors">
            <p class="font-medium text-text">{{ $post->title }}</p>
            <p class="text-sm text-muted mt-1">{{ $post->category?->name ?? 'Umum' }} · {{ $post->published_at->translatedFormat('d M Y') }}</p>
            @if ($post->excerpt)<p class="text-sm text-secondary mt-2">{{ $post->excerpt }}</p>@endif
        </a>
    @empty
        <p class="text-sm text-muted">Tidak ada berita ditemukan.</p>
    @endforelse
</div>

<div class="mt-6">{{ $posts->links() }}</div>
@endsection
