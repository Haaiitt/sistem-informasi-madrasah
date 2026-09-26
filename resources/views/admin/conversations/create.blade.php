@extends('layouts.app')
@section('title', 'Mulai Percakapan')

@section('content')
<div class="max-w-lg mx-auto">
    <a href="{{ route('admin.conversations.index') }}" class="text-sm text-muted hover:text-text transition-colors">&larr; Pesan Peserta</a>
    <h1 class="text-xl font-semibold text-text mt-2 mb-6">Mulai Percakapan</h1>

    <div class="bg-surface border border-border rounded-lg p-6">
        <form method="POST" action="{{ route('admin.conversations.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="user_id" class="block text-sm font-medium text-text mb-1">Peserta</label>
                <select id="user_id" name="user_id" class="w-full rounded-md border border-border px-3 py-2 text-sm" required>
                    <option value="">Pilih peserta...</option>
                    @foreach ($participants as $participant)
                        <option value="{{ $participant->id }}">{{ $participant->name }} ({{ $participant->username }})</option>
                    @endforeach
                </select>
                @error('user_id')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="subject" class="block text-sm font-medium text-text mb-1">Subjek</label>
                <input id="subject" type="text" name="subject" value="{{ old('subject') }}" class="w-full rounded-md border border-border px-3 py-2 text-sm" required>
                @error('subject')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="body" class="block text-sm font-medium text-text mb-1">Pesan</label>
                <textarea id="body" name="body" rows="4" class="w-full rounded-md border border-border px-3 py-2 text-sm" required>{{ old('body') }}</textarea>
                @error('body')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Kirim</button>
        </form>
    </div>
</div>
@endsection
