<?php

namespace App\Http\Requests;

use App\Rules\ValidRecaptcha;
use Illuminate\Foundation\Http\FormRequest;

class GuardianRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // publik, tanpa login
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            // BR-19: 4-30 karakter huruf kecil, angka, titik, garis bawah.
            'username' => ['required', 'string', 'regex:/^[a-z0-9._]{4,30}$/', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'max:20'],
            'g-recaptcha-response' => ['required', new ValidRecaptcha],
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'Username harus 4-30 karakter, huruf kecil, angka, titik, atau garis bawah.',
        ];
    }
}
