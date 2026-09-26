<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class ValidRecaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        // Dilewati saat testing otomatis — tidak realistis memanggil Google beneran di test.
        if (app()->environment('testing')) {
            return;
        }

        if (! $value) {
            $fail('Verifikasi CAPTCHA gagal, silakan coba lagi.');

            return;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $value,
        ]);

        $result = $response->json();

        if (! ($result['success'] ?? false) || ($result['score'] ?? 0) < 0.5) {
            $fail('Verifikasi CAPTCHA gagal, silakan coba lagi.');
        }
    }
}
