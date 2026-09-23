<div class="flex items-center gap-4 justify-end">
    @can('update', $page)
        <a href="{{ route('admin.pages.edit', $page) }}" class="text-sm font-medium text-secondary hover:text-primary transition-colors">Ubah</a>
    @endcan
    @can('delete', $page)
        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST"
              onsubmit="return confirm('Hapus halaman \'{{ $page->title }}\'?');">
            @csrf @method('DELETE')
            <button type="submit" class="text-sm font-medium text-danger hover:text-danger/80 transition-colors">Hapus</button>
        </form>
    @endcan
</div>
