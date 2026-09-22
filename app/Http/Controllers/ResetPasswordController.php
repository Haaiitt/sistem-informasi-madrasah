<?php

namespace App\Http\Controllers;

use App\Services\PasswordResetLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function __construct(private PasswordResetLinkService $links)
    {
    }

    public function show(string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'valid' => (bool) $this->links->findValidLink($token),
        ]);
    }

    public function update(Request $request, string $token): RedirectResponse
    {
        $link = $this->links->findValidLink($token);

        if (! $link) {
            return back()->withErrors(['token' => 'Tautan ini sudah tidak berlaku. Minta admin menerbitkan tautan baru.']);
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $this->links->consume($link, $request->string('password'));

        return redirect()->route('login')->with('status', 'Password berhasil diperbarui. Silakan masuk dengan password baru.');
    }
}
