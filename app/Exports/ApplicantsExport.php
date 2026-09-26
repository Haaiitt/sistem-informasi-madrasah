<?php

namespace App\Exports;

use App\Models\AdmissionWave;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ApplicantsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private AdmissionWave $wave)
    {
    }

    public function collection(): Collection
    {
        return $this->wave->applicants()->with('guardians')->get();
    }

    public function headings(): array
    {
        return [
            'No. Registrasi', 'Nama Lengkap', 'Jenis', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir',
            'NISN', 'NIK', 'Alamat', 'Status', 'Nama Wali', 'No. HP Wali', 'Waktu Dikirim',
        ];
    }

    public function map($applicant): array
    {
        $primaryGuardian = $applicant->guardians->firstWhere('is_primary_contact', true) ?? $applicant->guardians->first();

        return [
            $applicant->registration_number,
            $applicant->full_name,
            $applicant->type === 'baru' ? 'Siswa Baru' : 'Pindahan',
            $applicant->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            $applicant->birth_place,
            $applicant->birth_date->format('Y-m-d'),
            $applicant->nisn,
            $applicant->nik,
            $applicant->address,
            str_replace('_', ' ', $applicant->status),
            $primaryGuardian?->full_name,
            $primaryGuardian?->phone,
            $applicant->submitted_at?->format('Y-m-d H:i'),
        ];
    }
}
