
<div class="flex items-center gap-4 justify-end">
    @can('update', $post)
        <a href="{{ route('admin.posts.edit', $post) }}" class="text-sm font-medium text-secondary hover:text-primary transition-colors">Ubah</a>
    @endcan
    @can('delete', $post)
        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST"
              onsubmit="return confirm('Hapus konten \'{{ $post->title }}\'?');">
            @csrf @method('DELETE')
            <button type="submit" class="text-sm font-medium text-danger hover:text-danger/80 transition-colors">Hapus</button>
        </form>
    @endcan
</div>
