<?php

use App\Models\AcademicYear;
use App\Models\AdmissionWave;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

function openWave(): AdmissionWave
{
    $year = AcademicYear::create(['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);
    $admin = User::factory()->create();

    return AdmissionWave::create([
        'academic_year_id' => $year->id, 'name' => 'Gelombang 1', 'created_by' => $admin->id,
        'opens_at' => now()->subDay(), 'status' => 'dibuka',
    ]);
}

test('orang tua dapat membuat akun saat gelombang dibuka', function () {
    openWave();

    $response = $this->post(route('ppdb.register'), [
        'name' => 'Budi Santoso',
        'username' => 'budi.santoso',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'phone' => '081234567890',
        'g-recaptcha-response' => 'dummy-token',
    ]);

    $response->assertRedirect(route('login'));
    $user = User::where('username', 'budi.santoso')->first();
    expect($user)->not->toBeNull();
    expect($user->hasRole('calon_siswa'))->toBeTrue();
});

test('registrasi ditolak jika tidak ada gelombang dibuka', function () {
    $response = $this->post(route('ppdb.register'), [
        'name' => 'Budi Santoso',
        'username' => 'budi.santoso2',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'phone' => '081234567890',
        'g-recaptcha-response' => 'dummy-token',
    ]);

    $response->assertSessionHasErrors('wave');
    expect(User::where('username', 'budi.santoso2')->exists())->toBeFalse();
});

test('username duplikat ditolak', function () {
    openWave();
    User::create(['name' => 'A', 'username' => 'sudahada', 'password' => bcrypt('x'), 'is_active' => true]);

    $response = $this->post(route('ppdb.register'), [
        'name' => 'Budi',
        'username' => 'sudahada',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'phone' => '08123',
        'g-recaptcha-response' => 'dummy-token',
    ]);

    $response->assertSessionHasErrors('username');
});
