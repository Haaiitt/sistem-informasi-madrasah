<?php

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

function makeUserWithRole(string $roleName): User
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

test('super admin dapat melihat daftar akun', function () {
    $this->actingAs(makeUserWithRole('super_admin'))
        ->get(route('admin.users.index'))
        ->assertOk();
});

// AZ-05: role tanpa permission users.view ditolak.
test('operator konten ditolak mengakses kelola akun', function () {
    $this->actingAs(makeUserWithRole('operator_konten'))
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('super admin dapat membuat akun baru dan menetapkan role', function () {
    $admin = makeUserWithRole('super_admin');
    $guruRole = Role::where('name', 'guru')->first();

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Guru Baru',
        'username' => 'guru.baru',
        'password' => 'password123',
        'role_ids' => [$guruRole->id],
    ]);

    $response->assertRedirect(route('admin.users.index'));
    $newUser = User::where('username', 'guru.baru')->first();
    expect($newUser)->not->toBeNull();
    expect($newUser->hasRole('guru'))->toBeTrue();
});

// database.md DB-04: perubahan role wajib tercatat di audit_logs.
test('perubahan role tercatat di audit log', function () {
    $admin = makeUserWithRole('super_admin');
    $target = makeUserWithRole('guru');
    $newRole = Role::where('name', 'kepala_madrasah')->first();

    $this->actingAs($admin)->put(route('admin.users.update', $target), [
        'name' => $target->name,
        'role_ids' => [$newRole->id],
    ]);

    expect(AuditLog::where('action', 'users.roles_assigned')
        ->where('auditable_id', $target->id)
        ->exists())->toBeTrue();
});

test('super admin dapat menonaktifkan akun', function () {
    $admin = makeUserWithRole('super_admin');
    $target = makeUserWithRole('guru');

    $this->actingAs($admin)->patch(route('admin.users.deactivate', $target));

    expect($target->fresh()->is_active)->toBeFalse();
});

test('username dengan format tidak valid ditolak', function () {
    $admin = makeUserWithRole('super_admin');
    $guruRole = Role::where('name', 'guru')->first();

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Test',
        'username' => 'AB', // huruf besar & kurang dari 4 karakter
        'password' => 'password123',
        'role_ids' => [$guruRole->id],
    ]);

    $response->assertSessionHasErrors('username');
});
