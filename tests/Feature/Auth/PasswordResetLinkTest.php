<?php

use App\Models\AuditLog;
use App\Models\PasswordResetLink;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

function makeAuthUserWithRole(string $roleName): User
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

test('super admin dapat menerbitkan tautan reset dan tercatat di audit log', function () {
    $admin = makeAuthUserWithRole('super_admin');
    $target = makeAuthUserWithRole('guru');

    $this->actingAs($admin)
        ->post(route('admin.users.reset-link', $target))
        ->assertRedirect();

    expect(PasswordResetLink::where('user_id', $target->id)->exists())->toBeTrue();
    expect(AuditLog::where('action', 'users.password_reset_link_issued')
        ->where('auditable_id', $target->id)
        ->exists())->toBeTrue();
});

// AZ-29: tanpa permission users.issue_reset_link ditolak.
test('pengguna tanpa permission tidak dapat menerbitkan tautan reset', function () {
    $guru = makeAuthUserWithRole('guru');
    $target = makeAuthUserWithRole('operator_konten');

    $this->actingAs($guru)
        ->post(route('admin.users.reset-link', $target))
        ->assertForbidden();
});

// AZ-31: Panitia PPDB tidak boleh menerbitkan tautan untuk akun internal (D-22, D-24).
test('panitia ppdb ditolak menerbitkan tautan reset untuk akun internal', function () {
    $panitia = makeAuthUserWithRole('panitia_ppdb');
    $target = makeAuthUserWithRole('admin_tu');

    $this->actingAs($panitia)
        ->post(route('admin.users.reset-link', $target))
        ->assertForbidden();
});

test('tautan reset yang valid dapat dipakai untuk set password baru', function () {
    $admin = makeAuthUserWithRole('super_admin');
    $target = makeAuthUserWithRole('guru');
    $service = app(App\Services\PasswordResetLinkService::class);
    $rawToken = $service->issue($target, $admin);

    $response = $this->post(route('password-reset.update', $rawToken), [
        'password' => 'passwordBaru123',
        'password_confirmation' => 'passwordBaru123',
    ]);

    $response->assertRedirect(route('login'));
    expect(Hash::check('passwordBaru123', $target->fresh()->password))->toBeTrue();
});

// AZ-28: tautan yang sudah dipakai ditolak.
test('tautan reset yang sudah dipakai tidak dapat dipakai lagi', function () {
    $admin = makeAuthUserWithRole('super_admin');
    $target = makeAuthUserWithRole('guru');
    $service = app(App\Services\PasswordResetLinkService::class);
    $rawToken = $service->issue($target, $admin);

    $this->post(route('password-reset.update', $rawToken), [
        'password' => 'passwordBaru123',
        'password_confirmation' => 'passwordBaru123',
    ]);

    // Coba pakai lagi dengan password lain.
    $response = $this->post(route('password-reset.update', $rawToken), [
        'password' => 'passwordLain123',
        'password_confirmation' => 'passwordLain123',
    ]);

    $response->assertSessionHasErrors('token');
});

// AZ-28: tautan kedaluwarsa ditolak.
test('tautan reset yang kedaluwarsa ditolak', function () {
    $admin = makeAuthUserWithRole('super_admin');
    $target = makeAuthUserWithRole('guru');
    $service = app(App\Services\PasswordResetLinkService::class);
    $rawToken = $service->issue($target, $admin);

    PasswordResetLink::where('user_id', $target->id)->update([
        'expires_at' => now()->subHour(),
    ]);

    $response = $this->post(route('password-reset.update', $rawToken), [
        'password' => 'passwordBaru123',
        'password_confirmation' => 'passwordBaru123',
    ]);

    $response->assertSessionHasErrors('token');
});
