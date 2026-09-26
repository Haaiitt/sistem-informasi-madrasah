<?php

namespace App\Http\Requests\Ppdb;

use Illuminate\Foundation\Http\FormRequest;

class StoreConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('startAsGuardian', \App\Models\Conversation::class);
    }

    public function rules(): array
    {
        return [
            'applicant_id' => ['nullable', 'exists:applicants,id'],
            'subject' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:2000'],
        ];
    }
}
