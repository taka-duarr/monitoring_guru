<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1B2F4E">
    <title>@yield('title', 'Portal Guru')</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- CSS Design System Links -->
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

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
                            50: '#F0F7FF',
                            100: '#EFF6FF',
                            500: '#2563EB',
                            600: '#1B2F4E',
                            700: '#1E3A5F',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    <script src="{{ asset('js/forms.js') }}"></script>
    <!-- Alpine.js (via CDN) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-neutral-50 font-sans text-slate-800 antialiased" x-data="{ sidebarCollapsed: false, mobileSidebarOpen: false }">

    <!-- Sidebar Container -->
    <aside class="sidebar"
           :class="{ 'sidebar-collapsed': sidebarCollapsed, 'sidebar-open': mobileSidebarOpen }">

        <!-- Logo & Nama Sistem -->
        <div class="sidebar-brand-wrapper">
            <div class="sidebar-logo-icon" title="SIMGURU">
                <span x-show="!sidebarCollapsed">SG</span>
                <span x-show="sidebarCollapsed">G</span>
            </div>
            <div class="sidebar-brand-info" x-show="!sidebarCollapsed">
                <span class="sidebar-brand-name">SIMGURU</span>
                <span class="sidebar-brand-sub">Guru</span>
            </div>
            <!-- Close button for mobile -->
            <button class="btn btn-ghost btn-sm p-1 md:hidden text-white ml-auto"
                    @click="mobileSidebarOpen = false"
                    aria-label="Close Sidebar"
                    style="color: white !important;">
                <i class="ti ti-x" style="font-size: 20px;"></i>
            </button>
        </div>

        <!-- Sidebar Navigation -->
        <div class="sidebar-content">
            <div class="sidebar-group-label" x-show="!sidebarCollapsed">Menu Guru</div>
            <ul class="sidebar-menu-list">
                <!-- Jadwal -->
                <li class="sidebar-menu-item-wrapper">
                    <a href="{{ route('guru.dashboard') }}"
                       class="sidebar-menu-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}"
                       :class="{ 'active': {{ request()->routeIs('guru.dashboard') ? 'true' : 'false' }} }"
                       data-tooltip="Jadwal">
                        <i class="ti ti-calendar"></i>
                        <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Jadwal Mengajar</span>
                    </a>
                </li>

                <!-- Izin -->
                <li class="sidebar-menu-item-wrapper">
                    <a href="{{ route('guru.izin') }}"
                       class="sidebar-menu-item {{ request()->routeIs('guru.izin') ? 'active' : '' }}"
                       :class="{ 'active': {{ request()->routeIs('guru.izin') ? 'true' : 'false' }} }"
                       data-tooltip="Izin">
                        <i class="ti ti-file-report"></i>
                        <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Pengajuan Izin</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            @php
                $user = Auth::user();
                $userName = $user->name ?? 'Guru';
                $userInitials = collect(explode(' ', $userName))->map(fn($n) => substr($n, 0, 1))->take(2)->join('');
                $userRole = 'Guru';
            @endphp

            <!-- Avatar -->
            <div class="sidebar-footer-avatar" title="{{ $userName }}">
                {{ $userInitials }}
            </div>

            <!-- Info Admin -->
            <div class="sidebar-footer-info" x-show="!sidebarCollapsed">
                <span class="sidebar-footer-name">{{ $userName }}</span>
                <span class="sidebar-footer-role capitalize">{{ $userRole }}</span>
            </div>

            <!-- Logout Form / Button -->
            <form action="{{ route('logout') }}" method="POST" class="m-0 sidebar-footer-logout-form" x-show="!sidebarCollapsed">
                @csrf
                <button type="submit" class="sidebar-footer-logout" title="Keluar">
                    <i class="ti ti-logout"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="main-content-wrapper" :class="{ 'sidebar-collapsed-wrapper': sidebarCollapsed }">
        <!-- Topbar / Header -->
        <header class="bg-brand-600 px-5 py-4 shadow-md flex items-center justify-between z-10 sticky top-0 text-white" style="height: var(--topbar-height, 64px);">
            <div class="flex items-center gap-3">
                <!-- Mobile & Desktop Sidebar Toggle -->
                <button class="btn btn-ghost btn-sm p-1 flex items-center justify-center text-white mr-2 hover:bg-white/10 active:bg-white/20 rounded-md"
                        @click="if (window.innerWidth >= 768) { sidebarCollapsed = !sidebarCollapsed } else { mobileSidebarOpen = !mobileSidebarOpen }"
                        aria-label="Toggle Sidebar"
                        style="width: 36px; height: 36px; border: none; background: transparent; color: white !important;">
                    <i class="ti ti-menu-2" style="font-size: 20px; color: white !important;"></i>
                </button>
                <div>
                    <h1 class="font-heading font-bold text-xl tracking-tight leading-none text-white" style="color: white !important;">Portal Guru</h1>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white hover:bg-white/30 transition-colors backdrop-blur-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </header>

        <!-- Main Content -->
        <main class="content-area">
            <x-toast />
            @yield('content')
        </main>
    </div>

    <!-- Mobile Sidebar Backdrop -->
    <div class="sidebar-backdrop md:hidden"
         x-show="mobileSidebarOpen"
         @click="mobileSidebarOpen = false"
         style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.4); z-index: 90; display: none;"
         x-transition:opacity>
    </div>

    <!-- Custom CSS for layout fixes -->
    <style>
        /* Collapsed wrapper adjustment */
        .sidebar-collapsed-wrapper {
            margin-left: 60px !important;
        }
        /* Backdrop responsive visibility */
        @media (min-width: 768px) {
            .sidebar-backdrop {
                display: none !important;
            }
        }
        @media (max-width: 767px) {
            .sidebar-backdrop {
                display: block;
            }
            .main-content-wrapper {
                margin-left: 0 !important;
            }
        }
    </style>

    @stack('scripts')
</body>
</html>
