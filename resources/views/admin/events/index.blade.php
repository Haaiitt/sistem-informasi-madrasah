@extends('layouts.app')

@section('title', 'Agenda Kegiatan')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-text">Agenda Kegiatan</h1>
            <p class="text-sm text-muted mt-1">Kelola jadwal kegiatan madrasah.</p>
        </div>
        @can('create', App\Models\Event::class)
        <a href="{{ route('admin.events.create') }}"
           class="inline-flex items-center gap-1.5 rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
            + Tambah Agenda
        </a>
        @endcan
    </div>

    @if ($events->isEmpty())
        <div class="text-center border border-dashed border-border rounded-lg py-16 px-4">
            <p class="font-medium text-text">Belum Ada Agenda</p>
            <p class="text-sm text-muted mt-1">Belum ada agenda kegiatan yang dibuat.</p>
        </div>
    @else
        <div class="hidden md:block bg-surface border border-border rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-background text-left text-xs font-medium text-muted uppercase tracking-wide">
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Lokasi</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($events as $event)
                        <tr class="hover:bg-background/60 transition-colors">
                            <td class="px-4 py-3 font-medium text-text">{{ $event->title }}</td>
                            <td class="px-4 py-3 text-muted">{{ $event->starts_at->translatedFormat('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 text-muted">{{ $event->location ?? '—' }}</td>
                            <td class="px-4 py-3">@include('admin.events.partials.status-badge', ['event' => $event])</td>
                            <td class="px-4 py-3">@include('admin.events.partials.row-actions', ['event' => $event])</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="md:hidden space-y-3">
            @foreach ($events as $event)
                <div class="bg-surface border border-border rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <p class="font-medium text-text">{{ $event->title }}</p>
                        @include('admin.events.partials.status-badge', ['event' => $event])
                    </div>
                    <p class="text-sm text-muted mt-1">{{ $event->starts_at->translatedFormat('d M Y, H:i') }} · {{ $event->location ?? '—' }}</p>
                    <div class="mt-3 pt-3 border-t border-border">
                        @include('admin.events.partials.row-actions', ['event' => $event])
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $events->links() }}</div>
    @endif
</div>
@endsection
