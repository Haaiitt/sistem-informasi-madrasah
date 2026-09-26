@extends('layouts.app')
@section('title', 'Pesan')

@section('content')
<div class="max-w-2xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-text">Pesan</h1>
        <a href="{{ route('ppdb.conversations.create') }}" class="text-sm font-medium text-primary hover:text-primary-dark transition-colors">+ Mulai Percakapan</a>
    </div>

    <section>
        <h2 class="text-sm font-semibold text-text mb-3">Percakapan Pribadi</h2>
        @forelse ($conversations as $conversation)
            <a href="{{ route('ppdb.conversations.show', $conversation) }}" class="block bg-surface border border-border rounded-lg p-4 mb-2 hover:border-primary transition-colors">
                <p class="font-medium text-text">{{ $conversation->subject }}</p>
                <p class="text-sm text-muted mt-1">{{ $conversation->last_message_at?->translatedFormat('d M Y, H:i') }}</p>
            </a>
        @empty
            <p class="text-sm text-muted">Belum ada percakapan.</p>
        @endforelse
    </section>

    <section>
        <h2 class="text-sm font-semibold text-text mb-3">Pengumuman</h2>
        @forelse ($announcements as $announcement)
            <div class="bg-surface border border-border rounded-lg p-4 mb-2">
                <p class="font-medium text-text">{{ $announcement->title }}</p>
                <p class="text-sm text-secondary mt-1">{{ $announcement->body }}</p>
                <p class="text-xs text-muted mt-2">{{ $announcement->created_at->translatedFormat('d M Y, H:i') }}</p>
            </div>
        @empty
            <p class="text-sm text-muted">Belum ada pengumuman.</p>
        @endforelse
    </section>
</div>
@endsection
