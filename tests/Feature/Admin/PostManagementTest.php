<?php

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PostCategorySeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    $this->seed(PostCategorySeeder::class);
});

function makeContentUser(string $roleName = 'operator_konten'): User
{
    $user = User::create([
        'name' => ucfirst($roleName),
        'username' => strtolower($roleName).'_'.uniqid(),
        'password' => Hash::make('password123'),
        'is_active' => true,
    ]);
    $user->syncRoles([Role::where('name', $roleName)->value('id')]);

    return $user;
}

test('operator konten dapat membuat konten dengan status draft', function () {
    $user = makeContentUser();
    $category = PostCategory::where('slug', 'berita')->first();

    $response = $this->actingAs($user)->post(route('admin.posts.store'), [
        'post_category_id' => $category->id,
        'title' => 'Pengumuman Libur Semester',
        'body' => '<p>Isi pengumuman</p>',
        'status' => 'draft',
    ]);

    $response->assertRedirect(route('admin.posts.index'));
    expect(Post::where('title', 'Pengumuman Libur Semester')->exists())->toBeTrue();
});

test('konten dengan skrip berbahaya dibersihkan sebelum disimpan', function () {
    $user = makeContentUser();

    $this->actingAs($user)->post(route('admin.posts.store'), [
        'title' => 'Test XSS',
        'body' => '<p>Halo</p><script>alert(1)</script>',
        'status' => 'draft',
    ]);

    $post = Post::where('title', 'Test XSS')->first();
    expect($post->body)->not->toContain('<script>');
});

// BR-10: status terjadwal wajib punya published_at di masa depan.
test('status terjadwal tanpa waktu terbit ditolak', function () {
    $user = makeContentUser();

    $response = $this->actingAs($user)->post(route('admin.posts.store'), [
        'title' => 'Test Terjadwal',
        'body' => '<p>Isi</p>',
        'status' => 'terjadwal',
    ]);

    $response->assertSessionHasErrors('published_at');
});

// AZ: role tanpa permission posts.create ditolak.
test('guru ditolak membuat konten', function () {
    $user = makeContentUser('guru');

    $this->actingAs($user)
        ->post(route('admin.posts.store'), ['title' => 'X', 'body' => 'X', 'status' => 'draft'])
        ->assertForbidden();
});

test('operator konten dapat menghapus konten (soft delete)', function () {
    $user = makeContentUser();
    $post = Post::create([
        'author_id' => $user->id,
        'title' => 'Akan Dihapus',
        'slug' => 'akan-dihapus',
        'body' => 'isi',
        'status' => 'draft',
    ]);

    $this->actingAs($user)->delete(route('admin.posts.destroy', $post));

    expect(Post::find($post->id))->toBeNull();
    expect(Post::withTrashed()->find($post->id))->not->toBeNull();
});

// BR-10: query publik hanya menampilkan status terbit dan published_at sudah lewat.
test('scope published hanya mengambil post yang benar-benar sudah terbit', function () {
    Post::create(['author_id' => makeContentUser()->id, 'title' => 'A', 'slug' => 'a', 'body' => 'x', 'status' => 'terbit', 'published_at' => now()->subDay()]);
    Post::create(['author_id' => makeContentUser()->id, 'title' => 'B', 'slug' => 'b', 'body' => 'x', 'status' => 'terbit', 'published_at' => now()->addDay()]);
    Post::create(['author_id' => makeContentUser()->id, 'title' => 'C', 'slug' => 'c', 'body' => 'x', 'status' => 'draft', 'published_at' => now()->subDay()]);

    expect(Post::published()->count())->toBe(1);
});
