@extends('layouts.app')
@section('title', 'Pengumuman')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-text">Pengumuman</h1>
        @can('sendBroadcast', App\Models\Announcement::class)
        <a href="{{ route('admin.announcements.create') }}" class="text-sm font-medium text-primary hover:text-primary-dark transition-colors">+ Kirim Pengumuman</a>
        @endcan
    </div>
    <div class="space-y-2">
        @forelse ($announcements as $announcement)
            <div class="bg-surface border border-border rounded-lg p-4">
                <p class="font-medium text-text">{{ $announcement->title }}</p>
                <p class="text-sm text-secondary mt-1">{{ $announcement->body }}</p>
                <p class="text-xs text-muted mt-2">
                    {{ $announcement->wave?->name ?? 'Semua peserta' }} · {{ $announcement->recipient_count }} penerima ·
                    {{ $announcement->created_at->translatedFormat('d M Y, H:i') }}
                </p>
            </div>
        @empty
            <p class="text-sm text-muted">Belum ada pengumuman.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $announcements->links() }}</div>
</div>
@endsection
