<div>
    <label for="title" class="block text-sm font-medium text-text mb-1">Judul</label>
    <input id="title" type="text" name="title" value="{{ old('title', $page->title ?? '') }}"
           class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
           required>
    @error('title')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
</div>

<div>
    <label for="body" class="block text-sm font-medium text-text mb-1">Isi Halaman</label>
    <textarea id="body" name="body" rows="10"
              class="w-full rounded-md border border-border px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
              required>{{ old('body', $page->body ?? '') }}</textarea>
    <p class="mt-1 text-xs text-muted">HTML dasar didukung (akan dibersihkan otomatis dari skrip berbahaya).</p>
    @error('body')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
</div>

<div>
    <label for="sort_order" class="block text-sm font-medium text-text mb-1">Urutan Menu</label>
    <input id="sort_order" type="number" name="sort_order" min="0"
           value="{{ old('sort_order', $page->sort_order ?? 0) }}"
           class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
    <p class="mt-1 text-xs text-muted">Angka lebih kecil tampil lebih dulu di menu.</p>
    @error('sort_order')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
</div>

<div x-data="{ status: '{{ old('status', $page->status ?? 'draft') }}' }">
    <label for="status" class="block text-sm font-medium text-text mb-1">Status</label>
    <select id="status" name="status" x-model="status"
            class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
        <option value="draft">Draft</option>
        <option value="terjadwal">Terjadwal</option>
        <option value="terbit">Terbit</option>
    </select>
    @error('status')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror

    <div class="mt-3" x-show="status === 'terjadwal'" x-cloak>
        <label for="published_at" class="block text-sm font-medium text-text mb-1">Waktu Terbit</label>
        <input id="published_at" type="datetime-local" name="published_at"
               value="{{ old('published_at', isset($page->published_at) ? $page->published_at->format('Y-m-d\TH:i') : '') }}"
               class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
        @error('published_at')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
    </div>
</div>
