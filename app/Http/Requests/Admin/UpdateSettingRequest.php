<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', \App\Models\Setting::class);
    }

    public function rules(): array
    {
        return [
            'school_name' => ['required', 'string', 'max:200'],
            'school_address' => ['nullable', 'string', 'max:500'],
            'school_phone' => ['nullable', 'string', 'max:30'],
            'school_email' => ['nullable', 'email', 'max:150'],
            'logo' => ['nullable', 'image', 'max:1024'],
        ];
    }
}
