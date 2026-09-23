<?php

use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

function makeEventUser(string $roleName = 'operator_konten'): User
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

test('operator konten dapat membuat agenda kegiatan', function () {
    $user = makeEventUser();

    $response = $this->actingAs($user)->post(route('admin.events.store'), [
        'title' => 'Ujian Tengah Semester',
        'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
        'status' => 'draft',
    ]);

    $response->assertRedirect(route('admin.events.index'));
    expect(Event::where('title', 'Ujian Tengah Semester')->exists())->toBeTrue();
});

test('waktu selesai sebelum waktu mulai ditolak', function () {
    $user = makeEventUser();

    $response = $this->actingAs($user)->post(route('admin.events.store'), [
        'title' => 'Agenda Salah',
        'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
        'ends_at' => now()->format('Y-m-d H:i:s'),
        'status' => 'draft',
    ]);

    $response->assertSessionHasErrors('ends_at');
});

test('guru ditolak membuat agenda kegiatan', function () {
    $user = makeEventUser('guru');

    $this->actingAs($user)
        ->post(route('admin.events.store'), ['title' => 'X', 'starts_at' => now()->addDay(), 'status' => 'draft'])
        ->assertForbidden();
});

test('operator konten dapat menghapus agenda (soft delete)', function () {
    $user = makeEventUser();
    $event = Event::create([
        'author_id' => $user->id,
        'title' => 'Akan Dihapus',
        'starts_at' => now()->addWeek(),
        'status' => 'draft',
    ]);

    $this->actingAs($user)->delete(route('admin.events.destroy', $event));

    expect(Event::find($event->id))->toBeNull();
    expect(Event::withTrashed()->find($event->id))->not->toBeNull();
});
