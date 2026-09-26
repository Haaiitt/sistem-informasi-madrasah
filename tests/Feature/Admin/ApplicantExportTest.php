<?php

use App\Models\AcademicYear;
use App\Models\AdmissionWave;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

test('panitia dapat mengekspor daftar pendaftar ke excel', function () {
    $panitia = User::create(['name' => 'Panitia', 'username' => 'panitia_'.uniqid(), 'password' => Hash::make('x'), 'is_active' => true]);
    $panitia->syncRoles([Role::where('name', 'panitia_ppdb')->value('id')]);

    $year = AcademicYear::create(['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);
    $wave = AdmissionWave::create([
        'academic_year_id' => $year->id, 'name' => 'Gelombang 1', 'created_by' => $panitia->id,
        'opens_at' => now()->subDay(), 'status' => 'dibuka',
    ]);

    $response = $this->actingAs($panitia)->get(route('admin.admission-waves.applicants.export', $wave));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('guru ditolak mengekspor daftar pendaftar', function () {
    $guru = User::create(['name' => 'Guru', 'username' => 'guru_'.uniqid(), 'password' => Hash::make('x'), 'is_active' => true]);
    $guru->syncRoles([Role::where('name', 'guru')->value('id')]);

    $year = AcademicYear::create(['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);
    $wave = AdmissionWave::create([
        'academic_year_id' => $year->id, 'name' => 'Gelombang 1', 'created_by' => $guru->id,
        'opens_at' => now()->subDay(), 'status' => 'dibuka',
    ]);

    $this->actingAs($guru)
        ->get(route('admin.admission-waves.applicants.export', $wave))
        ->assertForbidden();
});
