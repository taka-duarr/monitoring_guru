<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2563eb">
    <title>@yield('title', 'Portal Siswa')</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], heading: ['Outfit', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#F0F7FF',
                            100: '#EFF6FF',
                            500: '#2563EB',
                            600: '#1B2F4E',
                            700: '#1E3A5F'
                        }
                    }
                }
            }
        }

    </script>
    <link class="js-forms" rel="stylesheet" href="{{ asset('css/forms.css') }}">
    <script src="{{ asset('js/forms.js') }}"></script>
    <!-- Alpine.js (via CDN) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-brand-600 px-5 py-4 shadow-md flex justify-between items-center z-10 sticky top-0 text-white">
        <div>
            <h1 class="font-heading font-bold text-white-800 text-xl tracking-tight">Portal Siswa</h1>
            <p class="text-xs text-brand-100 opacity-90">{{ Auth::user()->name }} (Ketua Kelas)</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white active:bg-white/30 transition-colors backdrop-blur-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </form>
    </header>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto relative p-5">
        <x-toast />

        @yield('content')
    </main>

</body>
</html>
