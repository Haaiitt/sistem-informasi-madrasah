<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Announcement extends Model
{
    const UPDATED_AT = null;

    protected $fillable = ['admission_wave_id', 'created_by', 'title', 'body', 'recipient_count'];

    public function wave(): BelongsTo
    {
        return $this->belongsTo(AdmissionWave::class, 'admission_wave_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'announcement_recipients')->withPivot('read_at');
    }
}
