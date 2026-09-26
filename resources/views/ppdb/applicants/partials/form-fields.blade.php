<div x-data="{ type: '{{ old('type', $applicant->type ?? 'baru') }}' }">
    <div>
        <label class="block text-sm font-medium text-text mb-2">Jenis Pendaftaran</label>
        <div class="flex gap-4 text-sm">
            <label class="flex items-center gap-2"><input type="radio" name="type" value="baru" x-model="type" {{ isset($applicant) ? 'disabled' : '' }}> Siswa Baru</label>
            <label class="flex items-center gap-2"><input type="radio" name="type" value="pindahan" x-model="type" {{ isset($applicant) ? 'disabled' : '' }}> Pindahan</label>
        </div>
        @error('type')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
        <div>
            <label for="full_name" class="block text-sm font-medium text-text mb-1">Nama Lengkap Anak</label>
            <input id="full_name" type="text" name="full_name" value="{{ old('full_name', $applicant->full_name ?? '') }}"
                   class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary" required>
            @error('full_name')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="gender" class="block text-sm font-medium text-text mb-1">Jenis Kelamin</label>
            <select id="gender" name="gender" class="w-full rounded-md border border-border px-3 py-2 text-sm" required>
                <option value="">Pilih</option>
                <option value="L" {{ old('gender', $applicant->gender ?? '') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ old('gender', $applicant->gender ?? '') === 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('gender')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="birth_place" class="block text-sm font-medium text-text mb-1">Tempat Lahir</label>
            <input id="birth_place" type="text" name="birth_place" value="{{ old('birth_place', $applicant->birth_place ?? '') }}"
                   class="w-full rounded-md border border-border px-3 py-2 text-sm" required>
            @error('birth_place')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="birth_date" class="block text-sm font-medium text-text mb-1">Tanggal Lahir</label>
            <input id="birth_date" type="date" name="birth_date" value="{{ old('birth_date', isset($applicant->birth_date) ? $applicant->birth_date->format('Y-m-d') : '') }}"
                   class="w-full rounded-md border border-border px-3 py-2 text-sm" required>
            @error('birth_date')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="nisn" class="block text-sm font-medium text-text mb-1">NISN <span class="text-muted font-normal">(opsional)</span></label>
            <input id="nisn" type="text" name="nisn" value="{{ old('nisn', $applicant->nisn ?? '') }}" maxlength="10"
                   class="w-full rounded-md border border-border px-3 py-2 text-sm">
            @error('nisn')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="nik" class="block text-sm font-medium text-text mb-1">NIK <span class="text-muted font-normal">(opsional)</span></label>
            <input id="nik" type="text" name="nik" value="{{ old('nik', $applicant->nik ?? '') }}" maxlength="16"
                   class="w-full rounded-md border border-border px-3 py-2 text-sm">
            @error('nik')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="mt-4">
        <label for="address" class="block text-sm font-medium text-text mb-1">Alamat</label>
        <textarea id="address" name="address" rows="2" class="w-full rounded-md border border-border px-3 py-2 text-sm" required>{{ old('address', $applicant->address ?? '') }}</textarea>
        @error('address')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
        @foreach (['village' => 'Kelurahan', 'district' => 'Kecamatan', 'city' => 'Kota/Kabupaten', 'province' => 'Provinsi'] as $field => $label)
            <div>
                <label for="{{ $field }}" class="block text-sm font-medium text-text mb-1">{{ $label }}</label>
                <input id="{{ $field }}" type="text" name="{{ $field }}" value="{{ old($field, $applicant->$field ?? '') }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm">
            </div>
        @endforeach
    </div>

    <div x-show="type === 'pindahan'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 pt-4 border-t border-border">
        <div>
            <label for="previous_school_name" class="block text-sm font-medium text-text mb-1">Sekolah Asal</label>
            <input id="previous_school_name" type="text" name="previous_school_name" value="{{ old('previous_school_name', $applicant->previous_school_name ?? '') }}"
                   class="w-full rounded-md border border-border px-3 py-2 text-sm">
            @error('previous_school_name')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="previous_grade" class="block text-sm font-medium text-text mb-1">Kelas Asal</label>
            <input id="previous_grade" type="number" min="1" max="6" name="previous_grade" value="{{ old('previous_grade', $applicant->previous_grade ?? '') }}"
                   class="w-full rounded-md border border-border px-3 py-2 text-sm">
            @error('previous_grade')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="target_grade" class="block text-sm font-medium text-text mb-1">Kelas Tujuan (permintaan)</label>
            <input id="target_grade" type="number" min="1" max="6" name="target_grade" value="{{ old('target_grade', $applicant->target_grade ?? '') }}"
                   class="w-full rounded-md border border-border px-3 py-2 text-sm">
            <p class="mt-1 text-xs text-muted">Penempatan akhir ditentukan Admin TU.</p>
            @error('target_grade')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
        </div>
    </div>

    @php
        $defaultGuardians = [['relation' => '', 'full_name' => '', 'occupation' => '', 'phone' => '', 'is_primary_contact' => false]];
        $guardiansData = old('guardians', ($applicant->guardians ?? collect())->toArray() ?: $defaultGuardians);
    @endphp
    <div class="mt-6 pt-4 border-t border-border" x-data="{ guardians: {{ Illuminate\Support\Js::from($guardiansData) }} }">
        <label class="block text-sm font-medium text-text mb-2">Data Orang Tua/Wali</label>
        <template x-for="(guardian, i) in guardians" :key="i">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-3 pb-3 border-b border-border last:border-0">
                <select :name="'guardians[' + i + '][relation]'" x-model="guardian.relation" class="rounded-md border border-border px-3 py-2 text-sm">
                    <option value="">Hubungan</option>
                    <option value="ayah">Ayah</option>
                    <option value="ibu">Ibu</option>
                    <option value="wali">Wali</option>
                </select>
                <input type="text" :name="'guardians[' + i + '][full_name]'" x-model="guardian.full_name" placeholder="Nama Lengkap" class="rounded-md border border-border px-3 py-2 text-sm">
                <input type="text" :name="'guardians[' + i + '][occupation]'" x-model="guardian.occupation" placeholder="Pekerjaan" class="rounded-md border border-border px-3 py-2 text-sm">
                <input type="text" :name="'guardians[' + i + '][phone]'" x-model="guardian.phone" placeholder="No. HP" class="rounded-md border border-border px-3 py-2 text-sm">
            </div>
        </template>
        <button type="button" @click="guardians.push({relation:'',full_name:'',occupation:'',phone:'',is_primary_contact:false})" class="text-sm font-medium text-primary hover:text-primary-dark transition-colors">
            + Tambah Wali
        </button>
        @error('guardians')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
    </div>
</div>
