<?php

use App\Models\AcademicYear;
use App\Models\AdmissionWave;
use App\Models\Applicant;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

function makeAdminTu(): User
{
    $user = User::create(['name' => 'Admin TU', 'username' => 'admintu_'.uniqid(), 'password' => Hash::make('x'), 'is_active' => true]);
    $user->syncRoles([Role::where('name', 'admin_tu')->value('id')]);

    return $user;
}

function daftarUlangApplicant(): Applicant
{
    $year = AcademicYear::create(['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);
    $admin = User::factory()->create();
    $wave = AdmissionWave::create([
        'academic_year_id' => $year->id, 'name' => 'Gelombang 1', 'created_by' => $admin->id,
        'opens_at' => now()->subDay(), 'status' => 'dibuka',
    ]);
    $guardianUser = User::factory()->create();

    $applicant = Applicant::create([
        'user_id' => $guardianUser->id, 'admission_wave_id' => $wave->id, 'academic_year_id' => $year->id,
        'registration_number' => 'PPDB-2026-0001', 'type' => 'baru', 'target_grade' => 1, 'status' => 'daftar_ulang',
        'full_name' => 'Anak Test', 'gender' => 'L', 'birth_place' => 'Jakarta',
        'birth_date' => '2018-01-01', 'address' => 'Jl. Contoh', 'submitted_at' => now(),
    ]);
    $applicant->guardians()->create(['relation' => 'ayah', 'full_name' => 'Bapak', 'phone' => '0812']);

    return $applicant;
}

test('admin tu dapat mengonversi pendaftar daftar ulang menjadi siswa', function () {
    $admin = makeAdminTu();
    $applicant = daftarUlangApplicant();

    $response = $this->actingAs($admin)->post(route('admin.applicants.convert', $applicant));

    $response->assertRedirect();
    $applicant->refresh();
    expect($applicant->status)->toBe('menjadi_siswa');

    $student = Student::where('applicant_id', $applicant->id)->first();
    expect($student)->not->toBeNull();
    expect($student->full_name)->toBe('Anak Test');
    expect($student->guardians)->toHaveCount(1);
});

// BR-07: hanya sekali.
test('pendaftar yang sudah dikonversi tidak dapat dikonversi lagi', function () {
    $admin = makeAdminTu();
    $applicant = daftarUlangApplicant();

    $this->actingAs($admin)->post(route('admin.applicants.convert', $applicant));
    $response = $this->actingAs($admin)->post(route('admin.applicants.convert', $applicant));

    $response->assertSessionHasErrors('status');
    expect(Student::where('applicant_id', $applicant->id)->count())->toBe(1);
});

test('panitia ditolak melakukan konversi (pemisahan tugas)', function () {
    $panitia = User::create(['name' => 'Panitia', 'username' => 'panitia_'.uniqid(), 'password' => Hash::make('x'), 'is_active' => true]);
    $panitia->syncRoles([Role::where('name', 'panitia_ppdb')->value('id')]);
    $applicant = daftarUlangApplicant();

    $this->actingAs($panitia)
        ->post(route('admin.applicants.convert', $applicant))
        ->assertForbidden();
});

test('pendaftar yang belum daftar ulang tidak dapat dikonversi', function () {
    $admin = makeAdminTu();
    $applicant = daftarUlangApplicant();
    $applicant->update(['status' => 'diterima']);

    $response = $this->actingAs($admin)->post(route('admin.applicants.convert', $applicant));

    $response->assertSessionHasErrors('status');
});
