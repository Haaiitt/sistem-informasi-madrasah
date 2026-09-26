<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistem Informasi Madrasah')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        {{-- Sidebar: tersembunyi di mobile, tetap terlihat di laptop ke atas (§3.4, §25) --}}
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-surface border-r border-border transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="h-16 flex items-center px-6 border-b border-border">
                <span class="font-semibold text-text">Sistem Informasi Madrasah</span>
            </div>
            <nav class="p-4 space-y-1">
                @can('create', App\Models\Applicant::class)
                <a href="{{ route('ppdb.applicants.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->routeIs('ppdb.applicants.*') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-background' }}">
                    Pendaftaran Anak
                </a>
                @endcan
                @can('viewAny', App\Models\Post::class)
                <a href="{{ route('admin.posts.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->routeIs('admin.posts.*') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-background' }}">
                    Berita & Pengumuman
                </a>
                @endcan
                @can('viewAny', App\Models\Page::class)
                <a href="{{ route('admin.pages.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->routeIs('admin.pages.*') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-background' }}">
                    Halaman Statis
                </a>
                @endcan
                @can('viewAny', App\Models\Event::class)
                <a href="{{ route('admin.events.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->routeIs('admin.events.*') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-background' }}">
                    Agenda Kegiatan
                </a>
                @endcan
                @can('viewAny', App\Models\Gallery::class)
                <a href="{{ route('admin.galleries.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->routeIs('admin.galleries.*') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-background' }}">
                    Galeri Foto
                </a>
                @endcan
                @can('viewAny', App\Models\Banner::class)
                <a href="{{ route('admin.banners.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->routeIs('admin.banners.*') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-background' }}">
                    Banner Beranda
                </a>
                @endcan
                @can('view', App\Models\Setting::class)
                <a href="{{ route('admin.settings.edit') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->routeIs('admin.settings.*') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-background' }}">
                    Identitas Madrasah
                </a>
                @endcan
                @can('viewAny', App\Models\AdmissionWave::class)
                <a href="{{ route('admin.admission-waves.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->routeIs('admin.admission-waves.*') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-background' }}">
                    Gelombang PPDB
                </a>
                @endcan
                @if (auth()->user()->hasRole('calon_siswa'))
                    <a href="{{ route('ppdb.conversations.index') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors
                            {{ request()->routeIs('ppdb.conversations.*') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-background' }}">
                        Pesan
                        @php($unread = auth()->user()->unreadMessageCount())
                        @if ($unread > 0)
                            <span class="inline-flex items-center justify-center rounded-full bg-danger text-white text-xs w-5 h-5">{{ $unread }}</span>
                        @endif
                    </a>
                @endif

                @can('viewAny', App\Models\Announcement::class)
                <a href="{{ route('admin.conversations.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->routeIs('admin.conversations.*') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-background' }}">
                    Pesan Peserta
                </a>
                <a href="{{ route('admin.announcements.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->routeIs('admin.announcements.*') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-background' }}">
                    Pengumuman
                </a>
                @endcan

                @can('viewAny', App\Models\PublicMessage::class)
                <a href="{{ route('admin.public-messages.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->routeIs('admin.public-messages.*') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-background' }}">
                    Pesan Publik
                </a>
                @endcan
                @can('viewAny', App\Models\User::class)
                <a href="{{ route('admin.users.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->routeIs('admin.users.*') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-background' }}">
                    Kelola Akun
                </a>
                @endcan
                <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->routeIs('profile.*') ? 'bg-primary/10 text-primary' : 'text-secondary hover:bg-background' }}">
                    Profil Saya
                </a>
            </nav>
        </aside>

        {{-- Overlay saat sidebar terbuka di mobile --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="fixed inset-0 z-20 bg-black/30 lg:hidden"></div>

        <div class="flex-1 flex flex-col min-w-0">
            {{-- Topbar --}}
            <header class="h-16 flex items-center justify-between px-4 sm:px-6 border-b border-border bg-surface">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 -ml-2 rounded-md hover:bg-background" aria-label="Buka menu">
                    <svg class="w-6 h-6 text-text" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <span class="text-sm text-muted">{{ auth()->user()->name }}</span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-secondary hover:text-danger transition-colors">
                        Keluar
                    </button>
                </form>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if (session('status'))
                    <div class="mb-6 rounded-md border border-success/20 bg-success-bg px-4 py-3 text-sm text-success" role="status">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('reset_link'))
                    <div class="mb-6 rounded-md border border-info/20 bg-info-bg px-4 py-3 text-sm text-info" role="status">
                        <p class="font-medium">Tautan reset password berhasil diterbitkan.</p>
                        <p class="mt-1 break-all text-text">{{ session('reset_link') }}</p>
                        <p class="mt-1 text-muted">Salin dan sampaikan ke pengguna di luar sistem (mis. WhatsApp). Tautan berlaku 24 jam dan hanya tampil sekali di sini.</p>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
