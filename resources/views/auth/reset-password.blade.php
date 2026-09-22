@extends('layouts.guest')

@section('title', 'Atur Password Baru')

@section('content')
    @if (! $valid)
        <div class="text-center">
            <p class="font-medium text-text">Tautan Tidak Berlaku</p>
            <p class="text-sm text-muted mt-1">Tautan ini sudah kedaluwarsa atau sudah pernah dipakai. Hubungi admin untuk menerbitkan tautan baru.</p>
        </div>
    @else
        <h2 class="text-base font-semibold text-text mb-1">Atur Password Baru</h2>
        <p class="text-sm text-muted mb-6">Password lama Anda tidak berlaku lagi setelah ini.</p>

        <form method="POST" action="{{ route('password-reset.update', $token) }}" class="space-y-5">
            @csrf
            <div>
                <label for="password" class="block text-sm font-medium text-text mb-1">Password Baru</label>
                <input id="password" type="password" name="password"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
                       required>
                <p class="mt-1 text-xs text-muted">Minimal 8 karakter.</p>
                @error('password')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-text mb-1">Ulangi Password Baru</label>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
                       required>
            </div>
            @error('token')<p class="text-sm text-danger">{{ $message }}</p>@enderror
            <button type="submit" class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
                Simpan Password Baru
            </button>
        </form>
    @endif
@endsection
