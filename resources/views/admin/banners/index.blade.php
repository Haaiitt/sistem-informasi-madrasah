@extends('layouts.app')

@section('title', 'Banner Beranda')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-text">Banner Beranda</h1>
            <p class="text-sm text-muted mt-1">Kelola banner yang tampil di halaman utama website.</p>
        </div>
        @can('create', App\Models\Banner::class)
        <a href="{{ route('admin.banners.create') }}"
           class="inline-flex items-center gap-1.5 rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
            + Tambah Banner
        </a>
        @endcan
    </div>

    @if ($banners->isEmpty())
        <div class="text-center border border-dashed border-border rounded-lg py-16 px-4">
            <p class="font-medium text-text">Belum Ada Banner</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($banners as $banner)
                <div class="bg-surface border border-border rounded-lg p-4 flex items-center gap-4">
                    <img src="{{ Storage::url($banner->image_path) }}" class="w-24 h-14 object-cover rounded-md border border-border shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-text truncate">{{ $banner->title ?? '(tanpa judul)' }}</p>
                        <p class="text-sm text-muted">Urutan {{ $banner->sort_order }} ·
                            {{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-4 shrink-0">
                        @can('update', $banner)
                            <a href="{{ route('admin.banners.edit', $banner) }}" class="text-sm font-medium text-secondary hover:text-primary transition-colors">Ubah</a>
                        @endcan
                        @can('delete', $banner)
                            <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST"
                                  onsubmit="return confirm('Hapus banner ini secara permanen?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-danger hover:text-danger/80 transition-colors">Hapus</button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $banners->links() }}</div>
    @endif
</div>
@endsection
