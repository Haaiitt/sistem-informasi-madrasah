<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $siteName) — {{ $siteName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased flex flex-col" x-data="{ menuOpen: false }">
    <header class="border-b border-border bg-surface">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold text-text">
                @if ($siteLogo)
                    <img src="{{ Storage::url($siteLogo) }}" class="w-8 h-8 object-contain">
                @endif
                {{ $siteName }}
            </a>

            <button @click="menuOpen = !menuOpen" class="sm:hidden p-2 -mr-2" aria-label="Buka menu">
                <svg class="w-6 h-6 text-text" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <nav class="hidden sm:flex items-center gap-6 text-sm font-medium text-secondary">
                <a href="{{ route('posts.index') }}" class="hover:text-primary transition-colors">Berita</a>
                <a href="{{ route('events.index') }}" class="hover:text-primary transition-colors">Agenda</a>
                <a href="{{ route('galleries.index') }}" class="hover:text-primary transition-colors">Galeri</a>
                <a href="{{ route('contact.create') }}" class="hover:text-primary transition-colors">Hubungi Kami</a>
                @foreach ($navPages as $navPage)
                    <a href="{{ route('pages.show', $navPage->slug) }}" class="hover:text-primary transition-colors">{{ $navPage->title }}</a>
                @endforeach
            </nav>
        </div>

        <nav x-show="menuOpen" x-cloak class="sm:hidden border-t border-border px-4 py-3 space-y-2 text-sm font-medium text-secondary">
            <a href="{{ route('posts.index') }}" class="block">Berita</a>
            <a href="{{ route('events.index') }}" class="block">Agenda</a>
            <a href="{{ route('galleries.index') }}" class="block">Galeri</a>
            <a href="{{ route('contact.create') }}" class="hover:text-primary transition-colors">Hubungi Kami</a>
            @foreach ($navPages as $navPage)
                <a href="{{ route('pages.show', $navPage->slug) }}" class="block">{{ $navPage->title }}</a>
            @endforeach
        </nav>
    </header>

    <main class="flex-1 max-w-5xl mx-auto w-full px-4 sm:px-6 py-8">
        @yield('content')
    </main>

    <footer class="border-t border-border bg-surface py-6 text-center text-sm text-muted">
        &copy; {{ now()->year }} {{ $siteName }}
    </footer>
</body>
</html>
