<?php

use App\Models\AcademicYear;
use App\Models\AdmissionWave;
use App\Models\Applicant;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

function makePanitia(): User
{
    $user = User::create(['name' => 'Panitia', 'username' => 'panitia_'.uniqid(), 'password' => Hash::make('x'), 'is_active' => true]);
    $user->syncRoles([Role::where('name', 'panitia_ppdb')->value('id')]);

    return $user;
}

function submittedApplicant(): Applicant
{
    $year = AcademicYear::create(['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);
    $admin = User::factory()->create();
    $wave = AdmissionWave::create([
        'academic_year_id' => $year->id, 'name' => 'Gelombang 1', 'created_by' => $admin->id,
        'opens_at' => now()->subDay(), 'status' => 'dibuka',
    ]);
    $guardian = User::factory()->create();

    return Applicant::create([
        'user_id' => $guardian->id, 'admission_wave_id' => $wave->id, 'academic_year_id' => $year->id,
        'registration_number' => 'PPDB-2026-0001', 'type' => 'baru', 'target_grade' => 1, 'status' => 'dikirim',
        'full_name' => 'Anak Test', 'gender' => 'L', 'birth_place' => 'Jakarta',
        'birth_date' => '2018-01-01', 'address' => 'Jl. Contoh', 'submitted_at' => now(),
    ]);
}

test('perlu_perbaikan wajib disertai catatan', function () {
    $panitia = makePanitia();
    $applicant = submittedApplicant();

    $response = $this->actingAs($panitia)->post(route('admin.applicants.verify', $applicant), ['result' => 'perlu_perbaikan']);

    $response->assertSessionHasErrors('review_note');
    expect($applicant->fresh()->status)->toBe('dikirim');
});

test('panitia dapat memverifikasi dan menetapkan hasil seleksi', function () {
    $panitia = makePanitia();
    $applicant = submittedApplicant();

    $this->actingAs($panitia)->post(route('admin.applicants.verify', $applicant), ['result' => 'terverifikasi']);
    expect($applicant->fresh()->status)->toBe('terverifikasi');

    $this->actingAs($panitia)->post(route('admin.applicants.decide', $applicant), ['result' => 'diterima']);
    expect($applicant->fresh()->status)->toBe('diterima');
});

test('hasil seleksi tidak dapat ditetapkan sebelum verifikasi', function () {
    $panitia = makePanitia();
    $applicant = submittedApplicant(); // masih 'dikirim'

    $response = $this->actingAs($panitia)->post(route('admin.applicants.decide', $applicant), ['result' => 'diterima']);

    $response->assertSessionHasErrors('status');
});

test('revisi hasil ditolak sebelum gelombang diumumkan', function () {
    $panitia = makePanitia();
    $applicant = submittedApplicant();
    $applicant->update(['status' => 'diterima']);

    $response = $this->actingAs($panitia)->post(route('admin.applicants.revise-decision', $applicant), [
        'result' => 'tidak_diterima', 'reason' => 'Salah input',
    ]);

    $response->assertSessionHasErrors('status');
});

test('guru ditolak memverifikasi pendaftar', function () {
    $user = User::create(['name' => 'Guru', 'username' => 'guru_'.uniqid(), 'password' => Hash::make('x'), 'is_active' => true]);
    $user->syncRoles([Role::where('name', 'guru')->value('id')]);
    $applicant = submittedApplicant();

    $this->actingAs($user)
        ->post(route('admin.applicants.verify', $applicant), ['result' => 'terverifikasi'])
        ->assertForbidden();
});
