@php
    $labels = ['draft' => ['Draft', 'muted'], 'terjadwal' => ['Terjadwal', 'warning'], 'terbit' => ['Terbit', 'success']];
    [$label, $tone] = $labels[$post->status] ?? ['—', 'muted'];
    $symbol = $tone === 'success' ? '●' : ($tone === 'warning' ? '◐' : '○');
@endphp
<span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium
             {{ $tone === 'success' ? 'bg-success-bg text-success' : ($tone === 'warning' ? 'bg-warning-bg text-warning' : 'bg-background text-muted') }}">
    {{ $symbol }} {{ $label }}
</span>
