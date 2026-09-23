<div>
    <label for="title" class="block text-sm font-medium text-text mb-1">Judul <span class="text-muted font-normal">(opsional)</span></label>
    <input id="title" type="text" name="title" value="{{ old('title', $banner->title ?? '') }}"
           class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
    @error('title')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
</div>

@isset($banner)
    <img src="{{ Storage::url($banner->image_path) }}" class="w-40 h-24 object-cover rounded-md border border-border">
@endisset
<div>
    <label for="image" class="block text-sm font-medium text-text mb-1">Gambar Banner {{ isset($banner) ? '(kosongkan jika tidak diganti)' : '' }}</label>
    <input id="image" type="file" name="image" accept="image/*"
           class="w-full text-sm text-text file:mr-3 file:rounded-md file:border-0 file:bg-background file:px-3 file:py-2 file:text-sm"
           {{ isset($banner) ? '' : 'required' }}>
    @error('image')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
</div>

<div>
    <label for="link_url" class="block text-sm font-medium text-text mb-1">Tautan Tujuan <span class="text-muted font-normal">(opsional)</span></label>
    <input id="link_url" type="url" name="link_url" value="{{ old('link_url', $banner->link_url ?? '') }}"
           placeholder="https://..."
           class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
    @error('link_url')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
</div>

<div>
    <label for="sort_order" class="block text-sm font-medium text-text mb-1">Urutan</label>
    <input id="sort_order" type="number" name="sort_order" min="0"
           value="{{ old('sort_order', $banner->sort_order ?? 0) }}"
           class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
    @error('sort_order')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
</div>

<label class="flex items-center gap-2 text-sm text-text">
    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }}
           class="rounded border-border text-primary focus:ring-primary/40">
    Aktif
</label>
