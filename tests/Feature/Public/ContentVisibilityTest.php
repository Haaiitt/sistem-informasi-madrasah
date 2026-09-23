<?php


use App\Models\Page;
use App\Models\Post;

test('halaman beranda publik dapat diakses tanpa login', function () {
    $this->get(route('home'))->assertOk();
});

test('post draft tidak dapat diakses publik', function () {
    $post = Post::create([
        'author_id' => seedAdminId(),
        'title' => 'Draft Rahasia',
        'slug' => 'draft-rahasia',
        'body' => 'isi',
        'status' => 'draft',
    ]);

    $this->get(route('posts.show', $post->slug))->assertNotFound();
});

test('post terjadwal di masa depan tidak dapat diakses publik', function () {
    $post = Post::create([
        'author_id' => seedAdminId(),
        'title' => 'Terjadwal',
        'slug' => 'terjadwal-besok',
        'body' => 'isi',
        'status' => 'terjadwal',
        'published_at' => now()->addDay(),
    ]);

    $this->get(route('posts.show', $post->slug))->assertNotFound();
});

test('post terbit dapat diakses publik', function () {
    $post = Post::create([
        'author_id' => seedAdminId(),
        'title' => 'Sudah Terbit',
        'slug' => 'sudah-terbit',
        'body' => 'isi',
        'status' => 'terbit',
        'published_at' => now()->subHour(),
    ]);

    $this->get(route('posts.show', $post->slug))->assertOk()->assertSee('Sudah Terbit');
});

test('pencarian berita berdasarkan judul', function () {
    Post::create(['author_id' => seedAdminId(), 'title' => 'Libur Semester', 'slug' => 'libur-semester', 'body' => 'x', 'status' => 'terbit', 'published_at' => now()->subDay()]);
    Post::create(['author_id' => seedAdminId(), 'title' => 'Jadwal Ujian', 'slug' => 'jadwal-ujian', 'body' => 'x', 'status' => 'terbit', 'published_at' => now()->subDay()]);

    $response = $this->get(route('posts.index', ['q' => 'Libur']));

    $response->assertSee('Libur Semester')->assertDontSee('Jadwal Ujian');
});

test('halaman statis draft tidak dapat diakses publik', function () {
    $page = Page::create(['author_id' => seedAdminId(), 'title' => 'Draft Page', 'slug' => 'draft-page', 'body' => 'x', 'status' => 'draft']);

    $this->get(route('pages.show', $page->slug))->assertNotFound();
});

function seedAdminId(): int
{
    return App\Models\User::firstOrCreate(
        ['username' => 'seed_author'],
        ['name' => 'Seed Author', 'password' => bcrypt('x'), 'is_active' => true]
    )->id;
}
