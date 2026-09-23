<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

class PostService
{
    public function create(array $data, int $authorId): Post
    {
        return Post::create($this->prepare($data, $authorId));
    }

    public function update(Post $post, array $data): Post
    {
        $post->update($this->prepare($data, $post->author_id, $post));

        return $post;
    }

    public function delete(Post $post): void
    {
        $post->delete(); // soft delete, sesuai database.md
    }

    private function prepare(array $data, int $authorId, ?Post $existing = null): array
    {
        $status = $data['status'];

        // FR-KON-02: 'terbit' tanpa published_at eksplisit berarti terbit sekarang.
        // 'terjadwal' wajib punya published_at di masa depan (divalidasi di Form Request).
        $publishedAt = $data['published_at'] ?? null;
        if ($status === 'terbit' && ! $publishedAt) {
            $publishedAt = now();
        }

        return [
            'post_category_id' => $data['post_category_id'] ?? null,
            'author_id' => $authorId,
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($data['title'], $existing?->id),
            'excerpt' => $data['excerpt'] ?? null,
            'body' => Purifier::clean($data['body']), // NFR: XSS pada isi konten
            'cover_image_path' => $data['cover_image_path'] ?? $existing?->cover_image_path,
            'status' => $status,
            'published_at' => $publishedAt,
        ];
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;

        while (
            Post::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
