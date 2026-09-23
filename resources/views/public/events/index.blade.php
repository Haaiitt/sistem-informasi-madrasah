@extends('layouts.public')
@section('title', 'Agenda Kegiatan')

@section('content')
<h1 class="text-xl font-semibold text-text mb-6">Agenda Kegiatan</h1>
<div class="space-y-4">
    @forelse ($events as $event)
        <div class="bg-surface border border-border rounded-lg p-4">
            <p class="font-medium text-text">{{ $event->title }}</p>
            <p class="text-sm text-muted mt-1">{{ $event->starts_at->translatedFormat('d M Y, H:i') }} @if($event->location) · {{ $event->location }} @endif</p>
            @if ($event->description)<p class="text-sm text-secondary mt-2">{{ $event->description }}</p>@endif
        </div>
    @empty
        <p class="text-sm text-muted">Belum ada agenda.</p>
    @endforelse
</div>
<div class="mt-6">{{ $events->links() }}</div>
@endsection
