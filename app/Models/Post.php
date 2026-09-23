<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'post_category_id', 'author_id', 'title', 'slug', 'excerpt',
        'body', 'cover_image_path', 'status', 'published_at',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // BR-10: tampil publik hanya jika status 'terbit' DAN published_at sudah lewat.
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'terbit')->where('published_at', '<=', now());
    }
}
