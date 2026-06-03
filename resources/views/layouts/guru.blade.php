<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#22c55e">
    <title>@yield('title', 'Portal Guru')</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                        }
                    }
                }
    </script>
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    <script src="{{ asset('js/forms.js') }}"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased h-screen flex flex-col pb-16">

    <!-- Header -->
    <header class="bg-white px-5 py-4 shadow-sm flex justify-between items-center z-10 sticky top-0">
        <div>
            <h1 class="font-heading font-bold text-xl text-slate-800 tracking-tight">Portal Guru</h1>
            <p class="text-xs text-slate-500">{{ Auth::user()->name }}</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-red-500 active:bg-slate-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </form>
    </header>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto relative">
        <x-toast />

        @yield('content')
    </main>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 w-full bg-white border-t border-slate-200 flex justify-around items-center h-16 pb-safe z-50">
        <a href="{{ route('guru.dashboard') }}" class="flex flex-col items-center justify-center w-full h-full {{ request()->routeIs('guru.dashboard') ? 'text-brand-600' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[10px] font-medium">Jadwal</span>
        </a>

        <a href="{{ route('guru.izin') }}" class="flex flex-col items-center justify-center w-full h-full {{ request()->routeIs('guru.izin') ? 'text-brand-600' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="text-[10px] font-medium">Izin</span>
        </a>
    </nav>
</body>
</html>
