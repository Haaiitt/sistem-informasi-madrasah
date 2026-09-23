<?php

use App\Models\Gallery;
use App\Models\GalleryPhoto;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    Storage::fake('public');
});

function makeGalleryUser(string $roleName = 'operator_konten'): User
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

test('operator konten dapat membuat album dengan sampul', function () {
    $user = makeGalleryUser();

    $response = $this->actingAs($user)->post(route('admin.galleries.store'), [
        'title' => 'Wisuda 2026',
        'cover' => UploadedFile::fake()->image('cover.jpg'),
    ]);

    $response->assertRedirect();
    $gallery = Gallery::where('title', 'Wisuda 2026')->first();
    expect($gallery)->not->toBeNull();


    Storage::disk('public')->assertExists($gallery->cover_image_path);
});

test('operator konten dapat menambah beberapa foto ke album', function () {
    $user = makeGalleryUser();
    $gallery = Gallery::create(['title' => 'Album Test', 'is_published' => false]);

    $response = $this->actingAs($user)->post(route('admin.galleries.photos.store', $gallery), [
        'photos' => [
            UploadedFile::fake()->image('a.jpg'),
            UploadedFile::fake()->image('b.jpg'),
        ],
    ]);

    $response->assertRedirect();
    expect($gallery->photos()->count())->toBe(2);
});

test('hapus foto juga menghapus berkas fisiknya', function () {
    $user = makeGalleryUser();
    $gallery = Gallery::create(['title' => 'Album Test', 'is_published' => false]);
    $path = UploadedFile::fake()->image('a.jpg')->store('galleries/photos', 'public');
    $photo = $gallery->photos()->create(['image_path' => $path, 'sort_order' => 1]);

    $this->actingAs($user)->delete(route('admin.galleries.photos.destroy', $photo));

    expect(GalleryPhoto::find($photo->id))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('guru ditolak membuat album', function () {
    $user = makeGalleryUser('guru');

    $this->actingAs($user)
        ->post(route('admin.galleries.store'), ['title' => 'X'])
        ->assertForbidden();
});
