<?php

use App\Models\AcademicYear;
use App\Models\AdmissionWave;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

function makeWaveUser(string $roleName = 'panitia_ppdb'): User
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

test('panitia dapat membuat gelombang baru berstatus dijadwalkan', function () {
    $user = makeWaveUser();
    $year = AcademicYear::create(['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);

    $response = $this->actingAs($user)->post(route('admin.admission-waves.store'), [
        'academic_year_id' => $year->id,
        'name' => 'Gelombang 1',
        'opens_at' => now()->addDay()->format('Y-m-d H:i:s'),
        'closes_at' => now()->addMonth()->format('Y-m-d H:i:s'),
    ]);

    $response->assertRedirect(route('admin.admission-waves.index'));
    $wave = AdmissionWave::where('name', 'Gelombang 1')->first();
    expect($wave->status)->toBe('dijadwalkan');
});

// BR-14: jadwal tumpang tindih ditolak.
test('gelombang dengan jadwal tumpang tindih ditolak', function () {
    $user = makeWaveUser();
    $year = AcademicYear::create(['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);

    AdmissionWave::create([
        'academic_year_id' => $year->id, 'name' => 'Gelombang 1', 'created_by' => $user->id,
        'opens_at' => now()->addDay(), 'closes_at' => now()->addMonth(), 'status' => 'dijadwalkan',
    ]);

    $response = $this->actingAs($user)->post(route('admin.admission-waves.store'), [
        'academic_year_id' => $year->id,
        'name' => 'Gelombang 2 Tumpang Tindih',
        'opens_at' => now()->addWeeks(2)->format('Y-m-d H:i:s'),
        'closes_at' => now()->addWeeks(6)->format('Y-m-d H:i:s'),
    ]);

    $response->assertSessionHasErrors('opens_at');
});

// BR-14: hanya satu gelombang boleh dibuka.
test('tidak dapat membuka gelombang jika sudah ada yang dibuka', function () {
    $user = makeWaveUser();
    $year = AcademicYear::create(['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);

    AdmissionWave::create([
        'academic_year_id' => $year->id, 'name' => 'Gelombang 1', 'created_by' => $user->id,
        'opens_at' => now()->subDay(), 'status' => 'dibuka',
    ]);
    $wave2 = AdmissionWave::create([
        'academic_year_id' => $year->id, 'name' => 'Gelombang 2', 'created_by' => $user->id,
        'opens_at' => now()->addMonth(), 'status' => 'dijadwalkan',
    ]);

    $response = $this->actingAs($user)->patch(route('admin.admission-waves.open', $wave2));

    $response->assertSessionHasErrors('status');
    expect($wave2->fresh()->status)->toBe('dijadwalkan');
});

test('scheduler membuka gelombang otomatis saat jadwal tercapai', function () {
    $user = makeWaveUser();
    $year = AcademicYear::create(['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);
    $wave = AdmissionWave::create([
        'academic_year_id' => $year->id, 'name' => 'Gelombang 1', 'created_by' => $user->id,
        'opens_at' => now()->subMinute(), 'status' => 'dijadwalkan',
    ]);

    $this->artisan('admission-waves:transition');

    expect($wave->fresh()->status)->toBe('dibuka');
});

test('guru ditolak membuat gelombang', function () {
    $user = makeWaveUser('guru');
    $year = AcademicYear::create(['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);

    $this->actingAs($user)
        ->post(route('admin.admission-waves.store'), [
            'academic_year_id' => $year->id, 'name' => 'X', 'opens_at' => now()->addDay(),
        ])
        ->assertForbidden();
});
