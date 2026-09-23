<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordResetLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PasswordResetLinkController extends Controller
{
    public function __construct(private PasswordResetLinkService $links)
    {
    }

    public function store(User $user): RedirectResponse
    {
        $this->authorize('issueResetLink', $user);

        $rawToken = $this->links->issue($user, Auth::user());

        return back()->with('reset_link', route('password-reset.show', ['token' => $rawToken]));
    }
}
