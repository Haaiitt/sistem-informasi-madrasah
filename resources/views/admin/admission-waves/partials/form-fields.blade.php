<div>
    <label for="academic_year_id" class="block text-sm font-medium text-text mb-1">Tahun Ajaran</label>
    <select id="academic_year_id" name="academic_year_id"
            class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary" required>
        @foreach ($academicYears as $year)
            <option value="{{ $year->id }}" {{ old('academic_year_id', $wave->academic_year_id ?? '') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
        @endforeach
    </select>
    @error('academic_year_id')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
</div>

<div>
    <label for="name" class="block text-sm font-medium text-text mb-1">Nama Gelombang</label>
    <input id="name" type="text" name="name" value="{{ old('name', $wave->name ?? '') }}"
           class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary" required>
    @error('name')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="opens_at" class="block text-sm font-medium text-text mb-1">Jadwal Buka</label>
        <input id="opens_at" type="datetime-local" name="opens_at"
               value="{{ old('opens_at', isset($wave->opens_at) ? $wave->opens_at->format('Y-m-d\TH:i') : '') }}"
               class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary" required>
        @error('opens_at')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="closes_at" class="block text-sm font-medium text-text mb-1">Jadwal Tutup <span class="text-muted font-normal">(opsional)</span></label>
        <input id="closes_at" type="datetime-local" name="closes_at"
               value="{{ old('closes_at', isset($wave->closes_at) ? $wave->closes_at->format('Y-m-d\TH:i') : '') }}"
               class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
        @error('closes_at')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
    </div>
</div>
