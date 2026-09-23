@extends('layouts.app')

@section('title', 'Berita & Pengumuman')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-text">Berita & Pengumuman</h1>
            <p class="text-sm text-muted mt-1">Kelola konten berita dan pengumuman madrasah.</p>
        </div>
        @can('create', App\Models\Post::class)
        <a href="{{ route('admin.posts.create') }}"
           class="inline-flex items-center gap-1.5 rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
            + Tulis Konten
        </a>
        @endcan
    </div>

    @if ($posts->isEmpty())
        <div class="text-center border border-dashed border-border rounded-lg py-16 px-4">
            <p class="font-medium text-text">Belum Ada Konten</p>
            <p class="text-sm text-muted mt-1">Belum ada berita atau pengumuman yang dibuat.</p>
        </div>
    @else
        <div class="hidden md:block bg-surface border border-border rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-background text-left text-xs font-medium text-muted uppercase tracking-wide">
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Penulis</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($posts as $post)
                        <tr class="hover:bg-background/60 transition-colors">
                            <td class="px-4 py-3 font-medium text-text">{{ $post->title }}</td>
                            <td class="px-4 py-3 text-muted">{{ $post->category?->name ?? '—' }}</td>
                            <td class="px-4 py-3">@include('admin.posts.partials.status-badge', ['post' => $post])</td>
                            <td class="px-4 py-3 text-muted">{{ $post->author->name }}</td>
                            <td class="px-4 py-3">@include('admin.posts.partials.row-actions', ['post' => $post])</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="md:hidden space-y-3">
            @foreach ($posts as $post)
                <div class="bg-surface border border-border rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <p class="font-medium text-text">{{ $post->title }}</p>
                        @include('admin.posts.partials.status-badge', ['post' => $post])
                    </div>
                    <p class="text-sm text-muted mt-1">{{ $post->category?->name ?? '—' }} · {{ $post->author->name }}</p>
                    <div class="mt-3 pt-3 border-t border-border">
                        @include('admin.posts.partials.row-actions', ['post' => $post])
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
