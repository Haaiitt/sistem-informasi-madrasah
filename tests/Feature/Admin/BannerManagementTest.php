<?php

use App\Models\Banner;
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

function makeBannerUser(string $roleName = 'operator_konten'): User
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

test('operator konten dapat menambah banner', function () {
    $user = makeBannerUser();

    $response = $this->actingAs($user)->post(route('admin.banners.store'), [
        'image' => UploadedFile::fake()->image('banner.jpg'),
        'sort_order' => 1,
    ]);

    $response->assertRedirect(route('admin.banners.index'));
    expect(Banner::count())->toBe(1);
});

test('hapus banner menghapus permanen berikut berkasnya', function () {
    $user = makeBannerUser();
    $path = UploadedFile::fake()->image('banner.jpg')->store('banners', 'public');
    $banner = Banner::create(['image_path' => $path, 'sort_order' => 1]);

    $this->actingAs($user)->delete(route('admin.banners.destroy', $banner));

    expect(Banner::find($banner->id))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('guru ditolak menambah banner', function () {
    $user = makeBannerUser('guru');

    $this->actingAs($user)
        ->post(route('admin.banners.store'), ['image' => UploadedFile::fake()->image('x.jpg')])
        ->assertForbidden();
});
