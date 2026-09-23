<div class="flex items-center gap-4 justify-end">
    @can('update', $event)
        <a href="{{ route('admin.events.edit', $event) }}" class="text-sm font-medium text-secondary hover:text-primary transition-colors">Ubah</a>
    @endcan
    @can('delete', $event)
        <form action="{{ route('admin.events.destroy', $event) }}" method="POST"
              onsubmit="return confirm('Hapus agenda \'{{ $event->title }}\'?');">
            @csrf @method('DELETE')
            <button type="submit" class="text-sm font-medium text-danger hover:text-danger/80 transition-colors">Hapus</button>
        </form>
    @endcan
</div>
