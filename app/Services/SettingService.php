<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SettingService
{
    public function update(array $data, ?UploadedFile $logo, int $updatedBy): void
    {
        $textFields = ['school_name', 'school_address', 'school_phone', 'school_email'];

        foreach ($textFields as $field) {
            Setting::updateOrCreate(
                ['name' => $field],
                ['value' => $data[$field] ?? null, 'updated_by' => $updatedBy]
            );
        }

        if ($logo) {
            $old = Setting::getValue('school_logo_path');
            if ($old) {
                Storage::disk('public')->delete($old);
            }

            Setting::updateOrCreate(
                ['name' => 'school_logo_path'],
                ['value' => $logo->store('settings', 'public'), 'updated_by' => $updatedBy]
            );
        }
    }
}
