<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Event::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', 'in:draft,terjadwal,terbit'],
            'published_at' => ['nullable', 'date', 'required_if:status,terjadwal', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'ends_at.after_or_equal' => 'Waktu selesai tidak boleh sebelum waktu mulai.',
            'published_at.required_if' => 'Waktu terbit wajib diisi untuk status terjadwal.',
            'published_at.after' => 'Waktu terbit harus di masa depan untuk status terjadwal.',
        ];
    }
}
