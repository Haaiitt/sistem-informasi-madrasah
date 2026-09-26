<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('startAsStaff', \App\Models\Conversation::class);
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'applicant_id' => ['nullable', 'exists:applicants,id'],
            'subject' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:2000'],
        ];
    }
}
