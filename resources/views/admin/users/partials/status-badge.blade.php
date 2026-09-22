@if ($user->is_active)
    <span class="inline-flex items-center gap-1 rounded-full bg-success-bg px-2.5 py-0.5 text-xs font-medium text-success">
        ● Aktif
    </span>
@else
    <span class="inline-flex items-center gap-1 rounded-full bg-background px-2.5 py-0.5 text-xs font-medium text-muted">
        ○ Nonaktif
    </span>
@endif
