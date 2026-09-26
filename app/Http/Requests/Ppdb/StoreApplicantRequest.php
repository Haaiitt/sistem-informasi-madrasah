<?php

namespace App\Http\Requests\Ppdb;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Applicant::class);
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:baru,pindahan'],
            'target_grade' => ['nullable', 'required_if:type,pindahan', 'integer', 'between:1,6'],
            'full_name' => ['required', 'string', 'max:150'],
            'gender' => ['required', 'in:L,P'],
            'birth_place' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date'],
            'nisn' => ['nullable', 'digits:10'],
            'nik' => ['nullable', 'digits:16'],
            'address' => ['required', 'string', 'max:255'],
            'village' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'previous_school_name' => ['required_if:type,pindahan', 'nullable', 'string', 'max:150'],
            'previous_grade' => ['required_if:type,pindahan', 'nullable', 'integer', 'between:1,6'],
            'guardians' => ['required', 'array', 'min:1'],
            'guardians.*.relation' => ['required', 'in:ayah,ibu,wali'],
            'guardians.*.full_name' => ['required', 'string', 'max:150'],
            'guardians.*.occupation' => ['nullable', 'string', 'max:100'],
            'guardians.*.phone' => ['nullable', 'string', 'max:20'],
            'guardians.*.is_primary_contact' => ['nullable', 'boolean'],
        ];
    }
}
