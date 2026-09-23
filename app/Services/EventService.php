<?php

namespace App\Services;

use App\Models\Event;
use Mews\Purifier\Facades\Purifier;

class EventService
{
    public function create(array $data, int $authorId): Event
    {
        return Event::create($this->prepare($data, $authorId));
    }

    public function update(Event $event, array $data): Event
    {
        $event->update($this->prepare($data, $event->author_id));

        return $event;
    }

    public function delete(Event $event): void
    {
        $event->delete();
    }

    private function prepare(array $data, int $authorId): array
    {
        $status = $data['status'];
        $publishedAt = $data['published_at'] ?? null;
        if ($status === 'terbit' && ! $publishedAt) {
            $publishedAt = now();
        }

        return [
            'author_id' => $authorId,
            'title' => $data['title'],
            'description' => isset($data['description']) ? Purifier::clean($data['description']) : null,
            'location' => $data['location'] ?? null,
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'] ?? null,
            'status' => $status,
            'published_at' => $publishedAt,
        ];
    }
}
