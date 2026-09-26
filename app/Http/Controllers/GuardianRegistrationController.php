<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardianRegistrationRequest;
use App\Services\GuardianRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GuardianRegistrationController extends Controller
{
    public function __construct(private GuardianRegistrationService $registration)
    {
    }

    public function create(): View
    {
        return view('ppdb.register', ['waveOpen' => $this->registration->waveIsOpen()]);
    }

    public function store(GuardianRegistrationRequest $request): RedirectResponse
    {
        if (! $this->registration->waveIsOpen()) {
            return back()->withErrors(['wave' => 'Pendaftaran belum dibuka saat ini.']);
        }

        $this->registration->register($request->validated());

        return redirect()->route('login')->with('status', 'Akun berhasil dibuat. Silakan masuk untuk mulai mendaftar.');
    }
}
