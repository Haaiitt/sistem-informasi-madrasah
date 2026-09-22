<?php

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

test('pengguna dapat mengganti password sendiri setelah login', function () {
    $user = User::create([
        'name' => 'Test User',
        'username' => 'testuser',
        'password' => Hash::make('passwordLama123'),
        'is_active' => true,
    ]);
    $user->syncRoles([Role::where('name', 'guru')->value('id')]);

    $response = $this->actingAs($user)->put(route('user-password.update'), [
        'current_password' => 'passwordLama123',
        'password' => 'passwordBaru123',
        'password_confirmation' => 'passwordBaru123',
    ]);

    $response->assertSessionHasNoErrors();
    expect(Hash::check('passwordBaru123', $user->fresh()->password))->toBeTrue();
});

test('ganti password ditolak jika password saat ini salah', function () {
    $user = User::create([
        'name' => 'Test User',
        'username' => 'testuser2',
        'password' => Hash::make('passwordLama123'),
        'is_active' => true,
    ]);
    $user->syncRoles([Role::where('name', 'guru')->value('id')]);

    $response = $this->actingAs($user)->put(route('user-password.update'), [
        'current_password' => 'salah',
        'password' => 'passwordBaru123',
        'password_confirmation' => 'passwordBaru123',
    ]);

    $response->assertSessionHasErrorsIn('updatePassword', 'current_password');
    expect(Hash::check('passwordLama123', $user->fresh()->password))->toBeTrue();
});
