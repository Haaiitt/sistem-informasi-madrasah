<?php

use App\Models\Announcement;
use App\Models\Conversation;
use App\Models\PublicMessage;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

function makeMsgUser(string $roleName): User
{
    $user = User::create(['name' => ucfirst($roleName), 'username' => strtolower($roleName).'_'.uniqid(), 'password' => Hash::make('x'), 'is_active' => true]);
    $user->syncRoles([Role::where('name', $roleName)->value('id')]);

    return $user;
}

test('calon siswa dapat memulai percakapan dan panitia dapat membalas', function () {
    $guardian = makeMsgUser('calon_siswa');
    $panitia = makeMsgUser('panitia_ppdb');

    $this->actingAs($guardian)->post(route('ppdb.conversations.store'), [
        'subject' => 'Tanya jadwal', 'body' => 'Kapan pengumuman hasil?',
    ]);

    $conversation = Conversation::where('subject', 'Tanya jadwal')->first();
    expect($conversation)->not->toBeNull();
    expect($conversation->started_by_side)->toBe('peserta');

    $this->actingAs($panitia)->post(route('admin.conversations.reply', $conversation), ['body' => 'Minggu depan.']);

    expect($conversation->messages()->count())->toBe(2);
});

// BR-21: hanya pemilik dan panitia berwenang yang boleh melihat.
test('calon siswa lain tidak dapat melihat percakapan yang bukan miliknya', function () {
    $owner = makeMsgUser('calon_siswa');
    $other = makeMsgUser('calon_siswa');
    $conversation = Conversation::create(['user_id' => $owner->id, 'subject' => 'X', 'started_by_side' => 'peserta', 'last_message_at' => now()]);

    $this->actingAs($other)->get(route('ppdb.conversations.show', $conversation))->assertForbidden();
});

test('guru ditolak melihat percakapan peserta manapun', function () {
    $owner = makeMsgUser('calon_siswa');
    $guru = makeMsgUser('guru');
    $conversation = Conversation::create(['user_id' => $owner->id, 'subject' => 'X', 'started_by_side' => 'peserta', 'last_message_at' => now()]);

    $this->actingAs($guru)->get(route('admin.conversations.show', $conversation))->assertForbidden();
});

test('membaca percakapan menandai pesan lawan bicara sebagai sudah dibaca', function () {
    $guardian = makeMsgUser('calon_siswa');
    $panitia = makeMsgUser('panitia_ppdb');
    $conversation = Conversation::create(['user_id' => $guardian->id, 'subject' => 'X', 'started_by_side' => 'peserta', 'last_message_at' => now()]);
    $conversation->messages()->create(['sender_id' => $panitia->id, 'sender_side' => 'admin', 'body' => 'Balasan panitia']);

    expect($guardian->unreadMessageCount())->toBe(1);

    $this->actingAs($guardian)->get(route('ppdb.conversations.show', $conversation));

    expect($guardian->unreadMessageCount())->toBe(0);
});

// DB-05: penerima pengumuman adalah snapshot saat kirim, bukan daftar dinamis.
test('pengumuman tersimpan sebagai snapshot penerima saat dikirim', function () {
    $panitia = makeMsgUser('panitia_ppdb');
    makeMsgUser('calon_siswa');
    makeMsgUser('calon_siswa');

    $response = $this->actingAs($panitia)->post(route('admin.announcements.store'), [
        'title' => 'Libur PPDB', 'body' => 'PPDB diliburkan sementara.',
    ]);

    $response->assertRedirect(route('admin.announcements.index'));
    $announcement = Announcement::where('title', 'Libur PPDB')->first();
    expect($announcement->recipient_count)->toBe(2);
    expect($announcement->recipients)->toHaveCount(2);

    // Peserta baru yang daftar SETELAH pengumuman dikirim tidak ikut jadi penerima.
    $newGuardian = makeMsgUser('calon_siswa');
    expect($announcement->recipients->contains($newGuardian))->toBeFalse();
});

test('guru ditolak mengirim pengumuman massal', function () {
    $guru = makeMsgUser('guru');

    $this->actingAs($guru)
        ->post(route('admin.announcements.store'), ['title' => 'X', 'body' => 'X'])
        ->assertForbidden();
});

test('pengunjung dapat mengirim pesan publik', function () {
    $response = $this->post(route('contact.store'), [
        'sender_name' => 'Budi', 'contact_phone' => '0812', 'category' => 'konsultasi_ppdb',
        'body' => 'Kapan PPDB dibuka?', 'g-recaptcha-response' => 'dummy',
    ]);

    $response->assertRedirect();
    expect(PublicMessage::where('sender_name', 'Budi')->exists())->toBeTrue();
});

// AZ-33: dua panitia tidak boleh menangani pesan yang sama secara bersamaan.
test('pesan publik yang sudah ditangani tidak dapat ditangani ulang', function () {
    $panitia = makeMsgUser('panitia_ppdb');
    $message = PublicMessage::create([
        'sender_name' => 'Budi', 'contact_phone' => '0812', 'category' => 'konsultasi_ppdb',
        'body' => 'Tanya', 'status' => 'baru',
    ]);

    $this->actingAs($panitia)->post(route('admin.public-messages.handle', $message), ['note' => 'Sudah dijawab.']);
    $response = $this->actingAs($panitia)->post(route('admin.public-messages.handle', $message), ['note' => 'Coba lagi.']);

    $response->assertSessionHasErrors('status');
    expect($message->fresh()->status)->toBe('ditangani');
});

test('isi pesan dibersihkan dari html/skrip berbahaya', function () {
    $guardian = makeMsgUser('calon_siswa');

    $this->actingAs($guardian)->post(route('ppdb.conversations.store'), [
        'subject' => 'Test', 'body' => '<script>alert(1)</script>Halo panitia',
    ]);

    $conversation = Conversation::where('subject', 'Test')->first();
    expect($conversation->messages->first()->body)->not->toContain('<script>');
});
