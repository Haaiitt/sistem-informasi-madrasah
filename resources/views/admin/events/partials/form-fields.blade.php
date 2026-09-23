<div>
    <label for="title" class="block text-sm font-medium text-text mb-1">Judul Kegiatan</label>
    <input id="title" type="text" name="title" value="{{ old('title', $event->title ?? '') }}"
           class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
           required>
    @error('title')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
</div>

<div>
    <label for="description" class="block text-sm font-medium text-text mb-1">Deskripsi <span class="text-muted font-normal">(opsional)</span></label>
    <textarea id="description" name="description" rows="4"
              class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">{{ old('description', $event->description ?? '') }}</textarea>
    @error('description')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
</div>

<div>
    <label for="location" class="block text-sm font-medium text-text mb-1">Lokasi <span class="text-muted font-normal">(opsional)</span></label>
    <input id="location" type="text" name="location" value="{{ old('location', $event->location ?? '') }}"
           class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
    @error('location')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="starts_at" class="block text-sm font-medium text-text mb-1">Waktu Mulai</label>
        <input id="starts_at" type="datetime-local" name="starts_at"
               value="{{ old('starts_at', isset($event->starts_at) ? $event->starts_at->format('Y-m-d\TH:i') : '') }}"
               class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
               required>
        @error('starts_at')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="ends_at" class="block text-sm font-medium text-text mb-1">Waktu Selesai <span class="text-muted font-normal">(opsional)</span></label>
        <input id="ends_at" type="datetime-local" name="ends_at"
               value="{{ old('ends_at', isset($event->ends_at) ? $event->ends_at->format('Y-m-d\TH:i') : '') }}"
               class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
        @error('ends_at')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
    </div>
</div>

<div x-data="{ status: '{{ old('status', $event->status ?? 'draft') }}' }">
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
               value="{{ old('published_at', isset($event->published_at) ? $event->published_at->format('Y-m-d\TH:i') : '') }}"
               class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
        @error('published_at')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
    </div>
</div>
