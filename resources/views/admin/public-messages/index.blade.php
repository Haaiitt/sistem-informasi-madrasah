@extends('layouts.app')
@section('title', 'Pesan Publik')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-xl font-semibold text-text mb-6">Pesan Publik</h1>
    <div class="space-y-3">
        @forelse ($messages as $publicMessage)
            <div class="bg-surface border border-border rounded-lg p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-medium text-text">{{ $publicMessage->sender_name }} — {{ $publicMessage->contact_phone }}</p>
                        <p class="text-xs text-muted mt-0.5">{{ str_replace('_', ' ', $publicMessage->category) }} · {{ $publicMessage->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                    <span class="shrink-0 inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium
                                 {{ $publicMessage->status === 'baru' ? 'bg-warning-bg text-warning' : 'bg-success-bg text-success' }}">
                        {{ $publicMessage->status === 'baru' ? '● Baru' : '✓ Ditangani' }}
                    </span>
                </div>
                <p class="text-sm text-secondary mt-2">{{ $publicMessage->body }}</p>

                @if ($publicMessage->status === 'baru')
                    @can('updateStatus', App\Models\PublicMessage::class)
                        <form method="POST" action="{{ route('admin.public-messages.handle', $publicMessage) }}" class="mt-3 pt-3 border-t border-border flex gap-2">
                            @csrf
                            <input type="text" name="note" placeholder="Catatan penanganan (opsional)" class="flex-1 rounded-md border border-border px-3 py-2 text-sm">
                            <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors whitespace-nowrap">Tandai Ditangani</button>
                        </form>
                    @endcan
                @else
                    <p class="text-xs text-muted mt-2">Ditangani oleh {{ $publicMessage->handler?->name }} · {{ $publicMessage->handled_at?->translatedFormat('d M Y, H:i') }}
                        @if ($publicMessage->handling_note) — {{ $publicMessage->handling_note }} @endif</p>
                @endif
            </div>
        @empty
            <p class="text-sm text-muted">Belum ada pesan masuk.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $messages->links() }}</div>
</div>
@endsection
