<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guardian extends Model
{
    protected $fillable = ['student_id', 'relation', 'full_name', 'occupation', 'phone', 'is_primary_contact'];

    protected function casts(): array
    {
        return ['is_primary_contact' => 'boolean'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
