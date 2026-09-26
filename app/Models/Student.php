<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'applicant_id', 'nisn', 'nik', 'full_name', 'gender', 'birth_place', 'birth_date',
        'address', 'village', 'district', 'city', 'province', 'postal_code',
        'status', 'entered_on', 'left_on',
    ];

    protected function casts(): array
    {
        return ['birth_date' => 'date', 'entered_on' => 'date', 'left_on' => 'date'];
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function guardians(): HasMany
    {
        return $this->hasMany(Guardian::class);
    }
}
