<?php

namespace App\Services;

use App\Models\Applicant;
use App\Models\ApplicantDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ApplicantDocumentService
{
    private const REQUIRED = ['kk', 'akta_kelahiran', 'pas_foto'];
    private const OPTIONAL = ['keterangan_tk_ra'];
    private const OPTIONAL_PINDAHAN = ['surat_pindah'];

    public function allowedTypes(Applicant $applicant): array
    {
        return array_merge(
            self::REQUIRED,
            self::OPTIONAL,
            $applicant->type === 'pindahan' ? self::OPTIONAL_PINDAHAN : []
        );
    }

    public function upload(Applicant $applicant, string $type, UploadedFile $file): ApplicantDocument
    {
        if (! in_array($type, $this->allowedTypes($applicant))) {
            throw ValidationException::withMessages(['type' => 'Jenis berkas tidak berlaku untuk pendaftaran ini.']);
        }

        // Mengunggah ulang menggantikan berkas lama (database.md).
        $existing = $applicant->documents()->where('type', $type)->first();
        if ($existing) {
            Storage::disk('local')->delete($existing->storage_path);
            $existing->delete();
        }

        // disk 'local' (Laravel 11+) = storage/app/private, TIDAK dapat diakses langsung (DB-10).
        $path = $file->store('applicant-documents/'.$applicant->id, 'local');

        return $applicant->documents()->create([
            'type' => $type,
            'storage_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size_bytes' => $file->getSize(),
        ]);
    }

    public function missingRequired(Applicant $applicant): array
    {
        $have = $applicant->documents()->pluck('type')->all();

        return array_values(array_diff(self::REQUIRED, $have));
    }
}
