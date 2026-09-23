<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';
    protected $description = 'Pindahkan status posts dari terjadwal ke terbit saat waktunya tiba (FR-KON-02)';

    public function handle(): void
    {
        $count = Post::where('status', 'terjadwal')
            ->where('published_at', '<=', now())
            ->update(['status' => 'terbit']);

        $this->info("{$count} post dipindahkan ke status terbit.");
    }
}
