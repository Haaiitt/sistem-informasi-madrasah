<?php

// use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('pengguna aktif dapat login dengan username dan password yang benar', function () {
    $user = User::create([
        'name' => 'Test User',
        'username' => 'testuser',
        'password' => Hash::make('password123'),
        'is_active' => true,
    ]);

    $response = $this->post('/login', [
        'username' => 'testuser',
        'password' => 'password123',
    ]);

    $response->assertRedirect();
    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->last_login_at)->not->toBeNull();
});

test('login gagal dengan password salah', function () {
    User::create([
        'name' => 'Test User',
        'username' => 'testuser',
        'password' => Hash::make('password123'),
        'is_active' => true,
    ]);

    $response = $this->post('/login', [
        'username' => 'testuser',
        'password' => 'salah',
    ]);

    $response->assertSessionHasErrors();
    $this->assertGuest();
});

// FR-AUTH-04: akun nonaktif tidak dapat login.
test('pengguna nonaktif tidak dapat login meski password benar', function () {
    User::create([
        'name' => 'Nonaktif',
        'username' => 'nonaktif',
        'password' => Hash::make('password123'),
        'is_active' => false,
    ]);

    $response = $this->post('/login', [
        'username' => 'nonaktif',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors();
    $this->assertGuest();
});

// architecture.md §5: rate limit 5 percobaan/menit per username+IP.
// architecture.md §5: rate limit 5 percobaan/menit per username+IP.
test('login diblokir setelah 5 percobaan gagal', function () {
    User::create([
        'name' => 'Test User',
        'username' => 'testuser',
        'password' => Hash::make('password123'),
        'is_active' => true,
    ]);

    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', ['username' => 'testuser', 'password' => 'salah']);
    }

    $response = $this->post('/login', ['username' => 'testuser', 'password' => 'password123']);

    $response->assertStatus(429);
    $this->assertGuest();
});
