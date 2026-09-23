@extends('layouts.app')

@section('title', 'Identitas Madrasah')

@section('content')
<div class="max-w-lg mx-auto">
    <h1 class="text-xl font-semibold text-text mb-6">Identitas Madrasah</h1>

    <div class="bg-surface border border-border rounded-lg p-6">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            @if ($settings['school_logo_path'] ?? null)
                <img src="{{ Storage::url($settings['school_logo_path']) }}" class="w-20 h-20 object-contain rounded-md border border-border">
            @endif
            <div>
                <label for="logo" class="block text-sm font-medium text-text mb-1">Logo <span class="text-muted font-normal">(opsional, kosongkan jika tidak diganti)</span></label>
                <input id="logo" type="file" name="logo" accept="image/*"
                       class="w-full text-sm text-text file:mr-3 file:rounded-md file:border-0 file:bg-background file:px-3 file:py-2 file:text-sm">
                @error('logo')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="school_name" class="block text-sm font-medium text-text mb-1">Nama Madrasah</label>
                <input id="school_name" type="text" name="school_name" value="{{ old('school_name', $settings['school_name'] ?? '') }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
                       required>
                @error('school_name')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="school_address" class="block text-sm font-medium text-text mb-1">Alamat</label>
                <textarea id="school_address" name="school_address" rows="3"
                          class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">{{ old('school_address', $settings['school_address'] ?? '') }}</textarea>
                @error('school_address')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="school_phone" class="block text-sm font-medium text-text mb-1">Telepon</label>
                <input id="school_phone" type="text" name="school_phone" value="{{ old('school_phone', $settings['school_phone'] ?? '') }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
                @error('school_phone')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="school_email" class="block text-sm font-medium text-text mb-1">Email</label>
                <input id="school_email" type="email" name="school_email" value="{{ old('school_email', $settings['school_email'] ?? '') }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
                @error('school_email')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Simpan</button>
        </form>
    </div>
</div>
@endsection
