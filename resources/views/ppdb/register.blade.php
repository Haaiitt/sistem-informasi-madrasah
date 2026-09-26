@extends('layouts.guest')
@section('title', 'Daftar Akun PPDB')

@section('content')
    @if (! $waveOpen)
        <div class="text-center">
            <p class="font-medium text-text">PPDB Belum Dibuka</p>
            <p class="text-sm text-muted mt-1">Saat ini belum ada gelombang pendaftaran yang dibuka. Silakan kembali lagi nanti.</p>
        </div>
    @else
        <h2 class="text-base font-semibold text-text mb-1">Daftar Akun PPDB</h2>
        <p class="text-sm text-muted mb-6">Untuk orang tua/wali calon siswa baru.</p>

        <form method="POST" action="{{ route('ppdb.register') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-text mb-1">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary" required>
                @error('name')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="username" class="block text-sm font-medium text-text mb-1">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary" required>
                <p class="mt-1 text-xs text-muted">Jangan gunakan NISN atau nama anak sebagai username.</p>
                @error('username')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-text mb-1">Nomor HP/WA</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary" required>
                @error('phone')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-text mb-1">Password</label>
                <input id="password" type="password" name="password"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary" required>
                @error('password')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-text mb-1">Ulangi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary" required>
            </div>
            <input type="hidden" name="g-recaptcha-response" id="recaptchaResponse">
            @error('g-recaptcha-response')<p class="text-sm text-danger">{{ $message }}</p>@enderror
            @error('wave')<p class="text-sm text-danger">{{ $message }}</p>@enderror

            <button type="submit" class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
                Daftar Akun
            </button>
        </form>

        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
        <script>
            document.querySelector('form').addEventListener('submit', function (e) {
                if (document.getElementById('recaptchaResponse').value) return;
                e.preventDefault();
                grecaptcha.ready(function () {
                    grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', { action: 'register' }).then(function (token) {
                        document.getElementById('recaptchaResponse').value = token;
                        e.target.closest('form').submit();
                    });
                });
            });
        </script>
    @endif
@endsection
