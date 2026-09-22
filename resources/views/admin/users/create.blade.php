@extends('layouts.app')

@section('title', 'Tambah Akun')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-muted hover:text-text transition-colors">&larr; Kelola Akun</a>
        <h1 class="text-xl font-semibold text-text mt-2">Tambah Akun</h1>
    </div>

    <div class="bg-surface border border-border rounded-lg p-6">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf
            @include('admin.users.partials.form-fields', ['roles' => $roles, 'selectedRoleIds' => old('role_ids', [])])

            <div>
                <label for="password" class="block text-sm font-medium text-text mb-1">Password</label>
                <input id="password" type="password" name="password"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
                       required>
                <p class="mt-1 text-xs text-muted">Minimal 8 karakter.</p>
                @error('password')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
                    Simpan
                </button>
                <a href="{{ route('admin.users.index') }}" class="rounded-md border border-border px-4 py-2 text-sm font-medium text-secondary hover:bg-background transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
