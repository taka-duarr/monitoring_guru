<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Monitoring Guru')</title>
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
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col shadow-sm z-10 hidden md:flex">
        <div class="h-16 flex items-center px-6 border-b border-slate-100">
            <h1 class="font-heading text-xl font-bold text-brand-600 tracking-tight">SMKN 2 SURABAYA</h1>
        </div>
        
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }} transition-colors">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>
            
            <p class="px-3 pt-4 pb-1.5 text-xs font-bold text-slate-400 uppercase tracking-wider">Data Master</p>

            <a href="{{ route('guru.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('guru.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }} transition-colors">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Manajemen Guru
            </a>

            <a href="{{ route('ketuakelas.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('ketuakelas.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }} transition-colors">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Ketua Kelas
            </a>

            <a href="{{ route('angkatan.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('angkatan.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }} transition-colors">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Angkatan
            </a>

            <a href="{{ route('kelas.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('kelas.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }} transition-colors">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Kelas
            </a>

            <a href="{{ route('jurusan.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('jurusan.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }} transition-colors">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Jurusan
            </a>

            <a href="{{ route('mapel.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('mapel.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }} transition-colors">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Mata Pelajaran
            </a>

            <a href="{{ route('ruangan.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('ruangan.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }} transition-colors">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                Ruangan
            </a>

            <p class="px-3 pt-4 pb-1.5 text-xs font-bold text-slate-400 uppercase tracking-wider">Operasional</p>

            <a href="{{ route('jadwalajar.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('jadwalajar.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }} transition-colors">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Jadwal Ajar
            </a>

            <a href="{{ route('absenmasuk.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('absenmasuk.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }} transition-colors">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                Rekap Absensi
            </a>

            <a href="{{ route('izin.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('izin.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }} transition-colors">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Pengajuan Izin
            </a>

            <a href="{{ route('statuskelas.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('statuskelas.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }} transition-colors">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Status Kelas
            </a>
        </div>
        
        <div class="p-4 border-t border-slate-100">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center w-full px-3 py-2.5 rounded-lg text-red-600 hover:bg-red-50 font-medium transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <!-- Topbar -->
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-6 z-20">
            <div class="flex items-center">
                <button class="md:hidden text-slate-500 hover:text-slate-700 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h2 class="font-heading text-lg font-semibold text-slate-800 ml-3 md:ml-0">@yield('page_title', 'Dashboard')</h2>
            </div>
            
            <div class="flex items-center">
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-slate-700">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500 capitalize">{{ str_replace('_', ' ', Auth::user()->jabatan) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center font-bold border border-brand-200">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Content area -->
        <div class="flex-1 overflow-y-auto p-6 bg-slate-50/50">
            <!-- SweetAlert2 Notifications -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    console.log('Menampilkan notifikasi success');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '{!! session("success") !!}',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
            </script>
            @endif
            
            @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    console.log('Menampilkan notifikasi error');
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: '{!! addslashes(session('error')) !!}',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true
                    });
                });
            </script>
            @endif

            @if($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Tidak Valid',
                        html: '{!! implode("<br>", $errors->all()) !!}',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                });
            </script>
            @endif

            @yield('content')
        </div>
    </main>

</body>
</html>
