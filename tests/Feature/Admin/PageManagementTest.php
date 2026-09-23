<?php

use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

function makePageUser(string $roleName = 'operator_konten'): User
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

test('operator konten dapat membuat halaman statis', function () {
    $user = makePageUser();

    $response = $this->actingAs($user)->post(route('admin.pages.store'), [
        'title' => 'Visi dan Misi',
        'body' => '<p>Isi visi misi</p>',
        'status' => 'draft',
        'sort_order' => 1,
    ]);

    $response->assertRedirect(route('admin.pages.index'));
    expect(Page::where('title', 'Visi dan Misi')->exists())->toBeTrue();
});

test('konten halaman dengan skrip berbahaya dibersihkan', function () {
    $user = makePageUser();

    $this->actingAs($user)->post(route('admin.pages.store'), [
        'title' => 'Test XSS Page',
        'body' => '<p>Halo</p><script>alert(1)</script>',
        'status' => 'draft',
    ]);

    $page = Page::where('title', 'Test XSS Page')->first();
    expect($page->body)->not->toContain('<script>');
});

test('guru ditolak membuat halaman statis', function () {
    $user = makePageUser('guru');

    $this->actingAs($user)
        ->post(route('admin.pages.store'), ['title' => 'X', 'body' => 'X', 'status' => 'draft'])
        ->assertForbidden();
});

test('operator konten dapat menghapus halaman (soft delete)', function () {
    $user = makePageUser();
    $page = Page::create([
        'author_id' => $user->id,
        'title' => 'Akan Dihapus',
        'slug' => 'akan-dihapus-page',
        'body' => 'isi',
        'status' => 'draft',
    ]);

    $this->actingAs($user)->delete(route('admin.pages.destroy', $page));

    expect(Page::find($page->id))->toBeNull();
    expect(Page::withTrashed()->find($page->id))->not->toBeNull();
});
