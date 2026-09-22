@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <h2 class="text-base font-semibold text-text mb-1">Masuk ke akun Anda</h2>
    <p class="text-sm text-muted mb-6">Gunakan username dan password yang terdaftar.</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <div>
            <label for="username" class="block text-sm font-medium text-text mb-1">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}"
                   class="w-full rounded-md border px-3 py-2 text-sm text-text placeholder:text-muted
                          focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary
                          {{ $errors->has('username') ? 'border-danger' : 'border-border' }}"
                   required autofocus autocomplete="username">
            @error('username')
                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-text mb-1">Password</label>
            <input id="password" type="password" name="password"
                   class="w-full rounded-md border border-border px-3 py-2 text-sm text-text
                          focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
                   required autocomplete="current-password">
        </div>

        <button type="submit" :disabled="submitting"
                class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-white
                       hover:bg-primary-dark transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
            <span x-show="!submitting">Masuk</span>
            <span x-show="submitting" x-cloak>Memproses...</span>
        </button>
    </form>
@endsection
