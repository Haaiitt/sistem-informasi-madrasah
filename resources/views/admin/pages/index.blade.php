@extends('layouts.app')

@section('title', 'Halaman Statis')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-text">Halaman Statis</h1>
            <p class="text-sm text-muted mt-1">Profil, visi misi, kontak, dan halaman lain.</p>
        </div>
        @can('create', App\Models\Page::class)
        <a href="{{ route('admin.pages.create') }}"
           class="inline-flex items-center gap-1.5 rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
            + Tambah Halaman
        </a>
        @endcan
    </div>

    @if ($pages->isEmpty())
        <div class="text-center border border-dashed border-border rounded-lg py-16 px-4">
            <p class="font-medium text-text">Belum Ada Halaman</p>
            <p class="text-sm text-muted mt-1">Belum ada halaman statis yang dibuat.</p>
        </div>
    @else
        <div class="hidden md:block bg-surface border border-border rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-background text-left text-xs font-medium text-muted uppercase tracking-wide">
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Urutan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Penulis</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($pages as $page)
                        <tr class="hover:bg-background/60 transition-colors">
                            <td class="px-4 py-3 font-medium text-text">{{ $page->title }}</td>
                            <td class="px-4 py-3 text-muted">{{ $page->sort_order }}</td>
                            <td class="px-4 py-3">@include('admin.pages.partials.status-badge', ['page' => $page])</td>
                            <td class="px-4 py-3 text-muted">{{ $page->author->name }}</td>
                            <td class="px-4 py-3">@include('admin.pages.partials.row-actions', ['page' => $page])</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="md:hidden space-y-3">
            @foreach ($pages as $page)
                <div class="bg-surface border border-border rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <p class="font-medium text-text">{{ $page->title }}</p>
                        @include('admin.pages.partials.status-badge', ['page' => $page])
                    </div>
                    <p class="text-sm text-muted mt-1">Urutan {{ $page->sort_order }} · {{ $page->author->name }}</p>
                    <div class="mt-3 pt-3 border-t border-border">
                        @include('admin.pages.partials.row-actions', ['page' => $page])
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $pages->links() }}</div>
    @endif
</div>
@endsection
