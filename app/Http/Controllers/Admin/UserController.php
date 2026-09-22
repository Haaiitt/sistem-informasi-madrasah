<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private UserManagementService $users)
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        return view('admin.users.index', [
            'users' => User::with('roles')->orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('admin.users.create', ['roles' => Role::orderBy('label')->get()]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = $this->users->create($request->validated());
        $this->users->assignRoles($user, $request->validated('role_ids'), $request->user());

        return redirect()->route('admin.users.index')->with('status', 'Akun berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', [
            'targetUser' => $user->load('roles'),
            'roles' => Role::orderBy('label')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->users->update($user, $request->validated());
        $this->users->assignRoles($user, $request->validated('role_ids'), $request->user());

        return redirect()->route('admin.users.index')->with('status', 'Akun berhasil diperbarui.');
    }

    public function deactivate(User $user): RedirectResponse
    {
        $this->authorize('deactivate', $user);
        $this->users->deactivate($user);

        return back()->with('status', 'Akun berhasil dinonaktifkan.');
    }

    public function activate(User $user): RedirectResponse
    {
        $this->authorize('deactivate', $user);
        $this->users->activate($user);

        return back()->with('status', 'Akun berhasil diaktifkan.');
    }
}