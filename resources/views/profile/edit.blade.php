@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-lg mx-auto">
    <h1 class="text-xl font-semibold text-text mb-1">Profil Saya</h1>
    <p class="text-sm text-muted mb-6">{{ auth()->user()->name }} ({{ auth()->user()->username }})</p>

    <div class="bg-surface border border-border rounded-lg p-6">
        <h2 class="text-sm font-semibold text-text mb-4">Ganti Password</h2>

        <form method="POST" action="{{ route('user-password.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-sm font-medium text-text mb-1">Password Saat Ini</label>
                <input id="current_password" type="password" name="current_password"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
                       required>
                @error('current_password', 'updatePassword')
                    <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-text mb-1">Password Baru</label>
                <input id="password" type="password" name="password"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
                       required>
                <p class="mt-1 text-xs text-muted">Minimal 8 karakter.</p>
                @error('password', 'updatePassword')
                    <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-text mb-1">Ulangi Password Baru</label>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
                       required>
            </div>

            <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
                Simpan Password Baru
            </button>
        </form>
    </div>
</div>
@endsection
