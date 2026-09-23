<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('post'));
    }

    public function rules(): array
    {
        return [
            'post_category_id' => ['nullable', 'exists:post_categories,id'],
            'title' => ['required', 'string', 'max:200'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'status' => ['required', 'in:draft,terjadwal,terbit'],
            'published_at' => ['nullable', 'date', 'required_if:status,terjadwal', 'after:now'],
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
