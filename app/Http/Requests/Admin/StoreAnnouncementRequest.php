<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('sendBroadcast', \App\Models\Announcement::class);
    }

    public function rules(): array
    {
        return [
            'admission_wave_id' => ['nullable', 'exists:admission_waves,id'],
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:2000'],
        ];
    }
}
