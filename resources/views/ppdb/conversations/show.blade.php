@extends('layouts.app')
@section('title', $conversation->subject)

@section('content')
<div class="max-w-xl mx-auto">
    <a href="{{ route('ppdb.conversations.index') }}" class="text-sm text-muted hover:text-text transition-colors">&larr; Pesan</a>
    <h1 class="text-xl font-semibold text-text mt-2 mb-6">{{ $conversation->subject }}</h1>

    <div class="space-y-3 mb-6">
        @foreach ($conversation->messages as $message)
            <div class="{{ $message->sender_side === 'peserta' ? 'ml-auto bg-primary/10' : 'mr-auto bg-surface border border-border' }} rounded-lg p-3 max-w-[80%] {{ $message->sender_side === 'peserta' ? 'text-right' : '' }}">
                <p class="text-sm text-text">{{ $message->body }}</p>
                <p class="text-xs text-muted mt-1">{{ $message->sender->name }} · {{ $message->created_at->translatedFormat('d M Y, H:i') }}</p>
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('ppdb.conversations.reply', $conversation) }}" class="flex gap-2">
        @csrf
        <input type="text" name="body" placeholder="Tulis balasan..." class="flex-1 rounded-md border border-border px-3 py-2 text-sm" required>
        <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Kirim</button>
    </form>
    @error('body')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
</div>
@endsection
