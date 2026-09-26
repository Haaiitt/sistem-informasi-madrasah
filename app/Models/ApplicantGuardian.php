<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantGuardian extends Model
{
    protected $fillable = ['applicant_id', 'relation', 'full_name', 'occupation', 'phone', 'is_primary_contact'];

    protected function casts(): array
    {
        return ['is_primary_contact' => 'boolean'];
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
