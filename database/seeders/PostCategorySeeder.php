<?php

namespace Database\Seeders;

use App\Models\PostCategory;
use Illuminate\Database\Seeder;

class PostCategorySeeder extends Seeder
{
    public function run(): void
    {
        PostCategory::firstOrCreate(['slug' => 'berita'], ['name' => 'Berita']);
        PostCategory::firstOrCreate(['slug' => 'pengumuman'], ['name' => 'Pengumuman']);
    }
}
