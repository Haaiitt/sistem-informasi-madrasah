<?php

use App\Models\AcademicYear;
use App\Models\AdmissionWave;
use App\Models\Applicant;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

function makeGuardianUser(): User
{
    $user = User::create([
        'name' => 'Orang Tua', 'username' => 'ortu_'.uniqid(),
        'password' => Hash::make('password123'), 'is_active' => true,
    ]);
    $user->syncRoles([Role::where('name', 'calon_siswa')->value('id')]);

    return $user;
}

function openWaveForTest(): AdmissionWave
{
    $year = AcademicYear::create(['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);
    $admin = User::factory()->create();

    return AdmissionWave::create([
        'academic_year_id' => $year->id, 'name' => 'Gelombang 1', 'created_by' => $admin->id,
        'opens_at' => now()->subDay(), 'status' => 'dibuka',
    ]);
}

function applicantPayload(array $overrides = []): array
{
    return array_merge([
        'type' => 'baru',
        'full_name' => 'Anak Test',
        'gender' => 'L',
        'birth_place' => 'Jakarta',
        'birth_date' => '2018-01-01',
        'address' => 'Jl. Contoh',
        'guardians' => [
            ['relation' => 'ayah', 'full_name' => 'Bapak Test', 'phone' => '0812'],
        ],
    ], $overrides);
}

test('orang tua dapat membuat draft pendaftaran saat gelombang dibuka', function () {
    openWaveForTest();
    $user = makeGuardianUser();

    $response = $this->actingAs($user)->post(route('ppdb.applicants.store'), applicantPayload());

    $response->assertRedirect();
    $applicant = Applicant::where('full_name', 'Anak Test')->first();
    expect($applicant)->not->toBeNull();
    expect($applicant->status)->toBe('draft');
    expect($applicant->target_grade)->toBe(1); // BR-17
    expect($applicant->guardians)->toHaveCount(1);
});

test('draft tidak dapat dibuat jika tidak ada gelombang dibuka', function () {
    $user = makeGuardianUser();

    $response = $this->actingAs($user)->post(route('ppdb.applicants.store'), applicantPayload());

    $response->assertSessionHasErrors();
    expect(Applicant::count())->toBe(0);
});

// BR-06: satu akun boleh lebih dari satu pendaftaran, dan hanya melihat miliknya sendiri.
test('orang tua tidak dapat mengakses pendaftaran milik akun lain', function () {
    openWaveForTest();
    $owner = makeGuardianUser();
    $other = makeGuardianUser();

    $applicant = Applicant::create(array_merge(applicantPayload(), [
        'user_id' => $owner->id, 'birth_date' => '2018-01-01', 'target_grade' => 1,
    ]));

    $this->actingAs($other)->get(route('ppdb.applicants.edit', $applicant))->assertForbidden();
});

test('pendaftaran berstatus dikirim tidak dapat diubah (BR-05)', function () {
    openWaveForTest();
    $user = makeGuardianUser();
    $applicant = Applicant::create([
        'user_id' => $user->id, 'type' => 'baru', 'target_grade' => 1, 'status' => 'dikirim',
        'full_name' => 'Anak Test', 'gender' => 'L', 'birth_place' => 'Jakarta',
        'birth_date' => '2018-01-01', 'address' => 'Jl. Contoh',
    ]);

    $response = $this->actingAs($user)->put(route('ppdb.applicants.update', $applicant), applicantPayload());

    $response->assertForbidden();
});

test('pindahan wajib isi sekolah asal dan kelas asal', function () {
    openWaveForTest();
    $user = makeGuardianUser();

    $response = $this->actingAs($user)->post(route('ppdb.applicants.store'), applicantPayload(['type' => 'pindahan']));

    $response->assertSessionHasErrors(['previous_school_name', 'previous_grade']);
});
