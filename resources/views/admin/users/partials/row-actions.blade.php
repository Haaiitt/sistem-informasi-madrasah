<div class="flex items-center gap-4 justify-end">
    <a href="{{ route('admin.users.edit', $user) }}" class="text-sm font-medium text-secondary hover:text-primary transition-colors">
        Ubah
    </a>
    @if ($user->is_active)
        <form action="{{ route('admin.users.deactivate', $user) }}" method="POST"
              onsubmit="return confirm('Nonaktifkan akun {{ $user->username }}? Pengguna ini tidak akan bisa masuk lagi sampai diaktifkan kembali.');">
            @csrf @method('PATCH')
            <button type="submit" class="text-sm font-medium text-danger hover:text-danger/80 transition-colors">
                Nonaktifkan
            </button>
        </form>
    @else
        <form action="{{ route('admin.users.activate', $user) }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit" class="text-sm font-medium text-success hover:text-success/80 transition-colors">
                Aktifkan
            </button>
        </form>
    @endif

    <form action="{{ route('admin.users.reset-link', $user) }}" method="POST"
        onsubmit="return confirm('Terbitkan tautan reset password untuk {{ $user->username }}?');">
        @csrf
        <button type="submit" class="text-sm font-medium text-secondary hover:text-primary transition-colors">
            Reset Password
        </button>
    </form>

</div>
