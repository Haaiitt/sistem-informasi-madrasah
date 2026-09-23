<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = ['author_id', 'title', 'slug', 'body', 'status', 'published_at', 'sort_order'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // BR-10 berlaku sama untuk pages (database.md: "Struktur sama dengan posts").
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'terbit')->where('published_at', '<=', now());
    }
}
