@extends('layouts.app')
@section('title', 'Pesan Peserta')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-text">Pesan Peserta</h1>
        <a href="{{ route('admin.conversations.create') }}" class="text-sm font-medium text-primary hover:text-primary-dark transition-colors">+ Mulai Percakapan</a>
    </div>
    <div class="space-y-2">
        @forelse ($conversations as $conversation)
            <a href="{{ route('admin.conversations.show', $conversation) }}" class="block bg-surface border border-border rounded-lg p-4 hover:border-primary transition-colors">
                <p class="font-medium text-text">{{ $conversation->subject }}</p>
                <p class="text-sm text-muted mt-1">{{ $conversation->user->name }} · {{ $conversation->last_message_at?->translatedFormat('d M Y, H:i') }}</p>
            </a>
        @empty
            <p class="text-sm text-muted">Belum ada percakapan.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $conversations->links() }}</div>
</div>
@endsection
