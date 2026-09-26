<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Applicant extends Model
{
    protected $fillable = [
        'user_id', 'admission_wave_id', 'academic_year_id', 'registration_number',
        'type', 'target_grade', 'status', 'full_name', 'gender', 'birth_place', 'birth_date',
        'nisn', 'nik', 'address', 'village', 'district', 'city', 'province', 'postal_code',
        'previous_school_name', 'previous_grade', 'review_note', 'reviewed_by', 'reviewed_at', 'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'reviewed_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wave(): BelongsTo
    {
        return $this->belongsTo(AdmissionWave::class, 'admission_wave_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function guardians(): HasMany
    {
        return $this->hasMany(ApplicantGuardian::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicantDocument::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ApplicantStatusHistory::class)->orderByDesc('created_at');
    }

    // BR-05: hanya draft & perlu_perbaikan yang boleh diubah orang tua.
    public function isEditableByGuardian(): bool
    {
        return in_array($this->status, ['draft', 'perlu_perbaikan']);
    }
}
