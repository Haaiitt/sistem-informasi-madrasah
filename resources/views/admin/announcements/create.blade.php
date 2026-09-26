@extends('layouts.app')
@section('title', 'Kirim Pengumuman')

@section('content')
<div class="max-w-lg mx-auto">
    <a href="{{ route('admin.announcements.index') }}" class="text-sm text-muted hover:text-text transition-colors">&larr; Pengumuman</a>
    <h1 class="text-xl font-semibold text-text mt-2 mb-6">Kirim Pengumuman</h1>

    <div class="bg-surface border border-border rounded-lg p-6">
        <form method="POST" action="{{ route('admin.announcements.store') }}" class="space-y-4" onsubmit="return confirm('Kirim pengumuman ini? Tidak dapat diubah setelah dikirim.');">
            @csrf
            <div>
                <label for="admission_wave_id" class="block text-sm font-medium text-text mb-1">Target</label>
                <select id="admission_wave_id" name="admission_wave_id" class="w-full rounded-md border border-border px-3 py-2 text-sm">
                    <option value="">Semua Peserta</option>
                    @foreach ($waves as $wave)
                        <option value="{{ $wave->id }}">Gelombang: {{ $wave->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="title" class="block text-sm font-medium text-text mb-1">Judul</label>
                <input id="title" type="text" name="title" value="{{ old('title') }}" class="w-full rounded-md border border-border px-3 py-2 text-sm" required>
                @error('title')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="body" class="block text-sm font-medium text-text mb-1">Isi Pengumuman</label>
                <textarea id="body" name="body" rows="4" class="w-full rounded-md border border-border px-3 py-2 text-sm" required>{{ old('body') }}</textarea>
                @error('body')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Kirim ke Semua Penerima</button>
        </form>
    </div>
</div>
@endsection
