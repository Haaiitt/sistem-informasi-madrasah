<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistem Informasi Madrasah')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-6">
            <h1 class="text-lg font-semibold text-text">Sistem Informasi Madrasah</h1>
        </div>
        <div class="bg-surface border border-border rounded-lg shadow-sm p-6 sm:p-8">
            @yield('content')
        </div>
    </div>
</body>
</html>
