<?php

use App\Models\AcademicYear;
use App\Models\AdmissionWave;
use App\Models\Applicant;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    Storage::fake('local');
});

function makeSubmitGuardian(): User
{
    $user = User::create(['name' => 'Ortu', 'username' => 'ortu_'.uniqid(), 'password' => Hash::make('x'), 'is_active' => true]);
    $user->syncRoles([Role::where('name', 'calon_siswa')->value('id')]);

    return $user;
}

function openWaveSubmit(): AdmissionWave
{
    $year = AcademicYear::create(['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);
    $admin = User::factory()->create();

    return AdmissionWave::create([
        'academic_year_id' => $year->id, 'name' => 'Gelombang 1', 'created_by' => $admin->id,
        'opens_at' => now()->subDay(), 'status' => 'dibuka',
    ]);
}

function draftApplicant(User $user): Applicant
{
    $applicant = Applicant::create([
        'user_id' => $user->id, 'type' => 'baru', 'target_grade' => 1, 'status' => 'draft',
        'full_name' => 'Anak Test', 'gender' => 'L', 'birth_place' => 'Jakarta',
        'birth_date' => '2018-01-01', 'address' => 'Jl. Contoh',
    ]);
    $applicant->guardians()->create(['relation' => 'ayah', 'full_name' => 'Bapak', 'phone' => '0812']);

    return $applicant;
}

test('pendaftaran tidak dapat dikirim jika berkas wajib belum lengkap', function () {
    $wave = openWaveSubmit();
    $user = makeSubmitGuardian();
    $applicant = draftApplicant($user);

    $response = $this->actingAs($user)->post(route('ppdb.applicants.submit', $applicant));

    $response->assertSessionHasErrors('documents');
    expect($applicant->fresh()->status)->toBe('draft');
});

test('pendaftaran berhasil dikirim setelah berkas lengkap, dapat nomor registrasi', function () {
    $wave = openWaveSubmit();
    $user = makeSubmitGuardian();
    $applicant = draftApplicant($user);

    foreach (['kk', 'akta_kelahiran', 'pas_foto'] as $type) {
        $this->actingAs($user)->post(route('ppdb.applicants.documents.store', $applicant), [
            'type' => $type,
            'file' => UploadedFile::fake()->create("$type.pdf", 100, 'application/pdf'),
        ]);
    }

    $response = $this->actingAs($user)->post(route('ppdb.applicants.submit', $applicant));

    $response->assertRedirect(route('ppdb.applicants.receipt', $applicant));
    $applicant->refresh();
    expect($applicant->status)->toBe('dikirim');
    expect($applicant->registration_number)->toStartWith('PPDB-2026-');
    expect($applicant->admission_wave_id)->toBe($wave->id);
    expect($applicant->statusHistories)->toHaveCount(1);
});

test('surat_pindah ditolak untuk pendaftaran jenis baru', function () {
    openWaveSubmit();
    $user = makeSubmitGuardian();
    $applicant = draftApplicant($user);

    $response = $this->actingAs($user)->post(route('ppdb.applicants.documents.store', $applicant), [
        'type' => 'surat_pindah',
        'file' => UploadedFile::fake()->create('x.pdf', 100, 'application/pdf'),
    ]);

    $response->assertSessionHasErrors('type');
});

// BR-04 tahap 2: dua pendaftaran NISN sama, tahun ajaran sama, ditolak saat kirim.
test('nisn yang sudah dipakai pendaftaran aktif lain ditolak saat kirim', function () {
    $wave = openWaveSubmit();
    $user1 = makeSubmitGuardian();
    $user2 = makeSubmitGuardian();

    $applicant1 = draftApplicant($user1);
    $applicant1->update(['nisn' => '1234567890', 'academic_year_id' => $wave->academic_year_id, 'status' => 'dikirim']);

    $applicant2 = draftApplicant($user2);
    $applicant2->update(['nisn' => '1234567890']);
    foreach (['kk', 'akta_kelahiran', 'pas_foto'] as $type) {
        $this->actingAs($user2)->post(route('ppdb.applicants.documents.store', $applicant2), [
            'type' => $type,
            'file' => UploadedFile::fake()->create("$type.pdf", 100, 'application/pdf'),
        ]);
    }

    $response = $this->actingAs($user2)->post(route('ppdb.applicants.submit', $applicant2));

    $response->assertSessionHasErrors('nisn');
});
