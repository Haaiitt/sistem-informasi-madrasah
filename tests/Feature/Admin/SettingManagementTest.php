<?php

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    $this->seed(SettingSeeder::class);
});

function makeSettingUser(string $roleName = 'operator_konten'): User
{
    $user = User::create([
        'name' => ucfirst($roleName),
        'username' => strtolower($roleName).'_'.uniqid(),
        'password' => Hash::make('password123'),
        'is_active' => true,
    ]);
    $user->syncRoles([Role::where('name', $roleName)->value('id')]);

    return $user;
}

test('operator konten dapat mengubah identitas madrasah', function () {
    $user = makeSettingUser();

    $response = $this->actingAs($user)->put(route('admin.settings.update'), [
        'school_name' => 'MTs Al-Hikmah',
        'school_address' => 'Jl. Contoh No. 1',
        'school_phone' => '021123456',
        'school_email' => 'info@alhikmah.sch.id',
    ]);

    $response->assertRedirect(route('admin.settings.edit'));
    expect(Setting::getValue('school_name'))->toBe('MTs Al-Hikmah');
});

test('guru ditolak mengubah identitas madrasah', function () {
    $user = makeSettingUser('guru');

    $this->actingAs($user)
        ->put(route('admin.settings.update'), ['school_name' => 'X'])
        ->assertForbidden();
});
