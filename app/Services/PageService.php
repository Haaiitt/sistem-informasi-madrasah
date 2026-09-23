<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

class PageService
{
    public function create(array $data, int $authorId): Page
    {
        return Page::create($this->prepare($data, $authorId));
    }

    public function update(Page $page, array $data): Page
    {
        $page->update($this->prepare($data, $page->author_id, $page));

        return $page;
    }

    public function delete(Page $page): void
    {
        $page->delete();
    }

    private function prepare(array $data, int $authorId, ?Page $existing = null): array
    {
        $status = $data['status'];
        $publishedAt = $data['published_at'] ?? null;
        if ($status === 'terbit' && ! $publishedAt) {
            $publishedAt = now();
        }

        return [
            'author_id' => $authorId,
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($data['title'], $existing?->id),
            'body' => Purifier::clean($data['body']),
            'status' => $status,
            'published_at' => $publishedAt,
            'sort_order' => $data['sort_order'] ?? 0,
        ];
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;

        while (
            Page::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
