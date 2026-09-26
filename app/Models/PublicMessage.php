<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicMessage extends Model
{
    protected $fillable = ['sender_name', 'contact_phone', 'category', 'body', 'status', 'handled_by', 'handled_at', 'handling_note'];

    protected function casts(): array
    {
        return ['handled_at' => 'datetime'];
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
