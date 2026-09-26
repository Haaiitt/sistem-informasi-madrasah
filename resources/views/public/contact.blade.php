@extends('layouts.public')
@section('title', 'Hubungi Kami')

@section('content')
<div class="max-w-lg mx-auto">
    <h1 class="text-xl font-semibold text-text mb-1">Hubungi Kami</h1>
    <p class="text-sm text-muted mb-6">Untuk pertanyaan PPDB atau masukan lainnya.</p>

    @if (session('status'))
        <div class="mb-6 rounded-md border border-success/20 bg-success-bg px-4 py-3 text-sm text-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('contact.store') }}" class="bg-surface border border-border rounded-lg p-6 space-y-4">
        @csrf
        <div>
            <label for="sender_name" class="block text-sm font-medium text-text mb-1">Nama</label>
            <input id="sender_name" type="text" name="sender_name" value="{{ old('sender_name') }}" class="w-full rounded-md border border-border px-3 py-2 text-sm" required>
            @error('sender_name')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="contact_phone" class="block text-sm font-medium text-text mb-1">No. HP/WA</label>
            <input id="contact_phone" type="text" name="contact_phone" value="{{ old('contact_phone') }}" class="w-full rounded-md border border-border px-3 py-2 text-sm" required>
            @error('contact_phone')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="category" class="block text-sm font-medium text-text mb-1">Kategori</label>
            <select id="category" name="category" class="w-full rounded-md border border-border px-3 py-2 text-sm" required>
                <option value="">Pilih...</option>
                <option value="konsultasi_ppdb">Konsultasi PPDB</option>
                <option value="feedback">Masukan</option>
                <option value="pemulihan_password">Pemulihan Password</option>
                <option value="lainnya">Lainnya</option>
            </select>
            @error('category')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="body" class="block text-sm font-medium text-text mb-1">Pesan</label>
            <textarea id="body" name="body" rows="4" class="w-full rounded-md border border-border px-3 py-2 text-sm" required>{{ old('body') }}</textarea>
            @error('body')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
        </div>
        <input type="hidden" name="g-recaptcha-response" id="recaptchaResponse">
        @error('g-recaptcha-response')<p class="text-sm text-danger">{{ $message }}</p>@enderror

        <button type="submit" class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Kirim Pesan</button>
    </form>

    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            if (document.getElementById('recaptchaResponse').value) return;
            e.preventDefault();
            grecaptcha.ready(function () {
                grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', { action: 'contact' }).then(function (token) {
                    document.getElementById('recaptchaResponse').value = token;
                    e.target.closest('form').submit();
                });
            });
        });
    </script>
</div>
@endsection
