<?php

namespace App\Http\Requests;

use App\Rules\ValidRecaptcha;
use Illuminate\Foundation\Http\FormRequest;

class PublicMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sender_name' => ['required', 'string', 'max:100'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'category' => ['required', 'in:konsultasi_ppdb,feedback,pemulihan_password,lainnya'],
            'body' => ['required', 'string', 'max:2000'],
            'g-recaptcha-response' => ['required', new ValidRecaptcha],
        ];
    }
}
