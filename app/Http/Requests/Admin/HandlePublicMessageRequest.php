<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HandlePublicMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateStatus', \App\Models\PublicMessage::class);
    }

    public function rules(): array
    {
        return ['note' => ['nullable', 'string', 'max:1000']];
    }
}
