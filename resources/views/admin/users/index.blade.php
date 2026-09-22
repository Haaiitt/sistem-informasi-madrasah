@extends('layouts.app')

@section('title', 'Kelola Akun')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-text">Kelola Akun</h1>
            <p class="text-sm text-muted mt-1">Buat akun pengguna dan atur role-nya.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center gap-1.5 rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
            + Tambah Akun
        </a>
    </div>

    @if ($users->isEmpty())
        <div class="text-center border border-dashed border-border rounded-lg py-16 px-4">
            <p class="font-medium text-text">Belum Ada Akun</p>
            <p class="text-sm text-muted mt-1">Belum ada akun pengguna yang dibuat.</p>
            <a href="{{ route('admin.users.create') }}" class="inline-block mt-4 text-sm font-medium text-primary hover:text-primary-dark">
                + Tambah Akun
            </a>
        </div>
    @else
        {{-- Desktop / laptop: tabel --}}
        <div class="hidden md:block bg-surface border border-border rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-background text-left text-xs font-medium text-muted uppercase tracking-wide">
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Username</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($users as $user)
                        <tr class="hover:bg-background/60 transition-colors">
                            <td class="px-4 py-3 font-medium text-text">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-muted">{{ $user->username }}</td>
                            <td class="px-4 py-3 text-muted">{{ $user->roles->pluck('label')->join(', ') }}</td>
                            <td class="px-4 py-3">
                                @include('admin.users.partials.status-badge', ['user' => $user])
                            </td>
                            <td class="px-4 py-3">
                                @include('admin.users.partials.row-actions', ['user' => $user])
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile: kartu, bukan tabel dipaksakan (§21) --}}
        <div class="md:hidden space-y-3">
            @foreach ($users as $user)
                <div class="bg-surface border border-border rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-medium text-text">{{ $user->name }}</p>
                            <p class="text-sm text-muted">{{ $user->username }}</p>
                        </div>
                        @include('admin.users.partials.status-badge', ['user' => $user])
                    </div>
                    <p class="text-sm text-muted mt-2">{{ $user->roles->pluck('label')->join(', ') }}</p>
                    <div class="mt-3 pt-3 border-t border-border">
                        @include('admin.users.partials.row-actions', ['user' => $user])
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $users->links() }}</div>
    @endif
</div>
@endsection
