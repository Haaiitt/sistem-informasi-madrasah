<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'school_name' => 'Nama Madrasah',
            'school_logo_path' => null,
            'school_address' => '',
            'school_phone' => '',
            'school_email' => '',
        ];

        foreach ($defaults as $name => $value) {
            Setting::firstOrCreate(['name' => $name], ['value' => $value]);
        }
    }
}
