<div>
    <label for="name" class="block text-sm font-medium text-text mb-1">Nama Lengkap</label>
    <input id="name" type="text" name="name" value="{{ old('name', $targetUser->name ?? '') }}"
           class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
           required>
    @error('name')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
</div>

@isset($targetUser)
<div>
    <label class="block text-sm font-medium text-text mb-1">Username</label>
    <input type="text" value="{{ $targetUser->username }}" disabled
           class="w-full rounded-md border border-border bg-background px-3 py-2 text-sm text-muted cursor-not-allowed">
    <p class="mt-1 text-xs text-muted">Username tidak dapat diubah.</p>
</div>
@else
<div>
    <label for="username" class="block text-sm font-medium text-text mb-1">Username</label>
    <input id="username" type="text" name="username" value="{{ old('username') }}"
           class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
           required>
    @error('username')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
</div>
@endisset

<div>
    <label for="phone" class="block text-sm font-medium text-text mb-1">No. HP <span class="text-muted font-normal">(opsional)</span></label>
    <input id="phone" type="text" name="phone" value="{{ old('phone', $targetUser->phone ?? '') }}"
           class="w-full rounded-md border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
</div>

<div>
    <label class="block text-sm font-medium text-text mb-2">Role</label>
    <div class="space-y-2">
        @foreach ($roles as $role)
            <label class="flex items-center gap-2 text-sm text-text cursor-pointer">
                <input type="checkbox" name="role_ids[]" value="{{ $role->id }}"
                       {{ in_array($role->id, $selectedRoleIds) ? 'checked' : '' }}
                       class="rounded border-border text-primary focus:ring-primary/40">
                {{ $role->label }}
            </label>
        @endforeach
    </div>
    @error('role_ids')<p class="mt-1 text-sm text-danger">{{ $message }}</p>@enderror
</div>
