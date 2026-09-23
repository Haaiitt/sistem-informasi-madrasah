<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Page::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'status' => ['required', 'in:draft,terjadwal,terbit'],
            'published_at' => ['nullable', 'date', 'required_if:status,terjadwal', 'after:now'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'published_at.required_if' => 'Waktu terbit wajib diisi untuk status terjadwal.',
            'published_at.after' => 'Waktu terbit harus di masa depan untuk status terjadwal.',
        ];
    }
}
