<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Informasi Monitoring Guru')</title>
    @php
        $appLogo = \App\Models\Setting::get('school_logo') && file_exists(public_path('storage/' . \App\Models\Setting::get('school_logo'))) 
            ? asset('storage/' . \App\Models\Setting::get('school_logo')) 
            : asset('favicon.ico');
    @endphp
    <link rel="icon" href="{{ $appLogo }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <!-- CSS Design System Links -->
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <!-- Tailwind CSS (via CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/forms.js') }}"></script>
    <script src="{{ asset('js/live-search.js') }}"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: 'var(--color-primary-50)',
                            100: 'var(--color-primary-100)',
                            200: 'var(--color-primary-200)',
                            300: 'var(--color-primary-300)',
                            400: 'var(--color-primary-400)',
                            500: 'var(--color-primary-500)',
                            600: 'var(--color-primary-600)',
                            700: 'var(--color-primary-700)',
                            800: 'var(--color-primary-800)',
                            900: 'var(--color-primary-900)',
                        },
                        success: {
                            50: 'var(--color-success-50)',
                            100: 'var(--color-success-100)',
                            500: 'var(--color-success-500)',
                            600: 'var(--color-success-600)',
                            700: 'var(--color-success-700)',
                            900: 'var(--color-success-900)',
                        },
                        warning: {
                            50: 'var(--color-warning-50)',
                            100: 'var(--color-warning-100)',
                            500: 'var(--color-warning-500)',
                            600: 'var(--color-warning-600)',
                            700: 'var(--color-warning-700)',
                            900: 'var(--color-warning-900)',
                        },
                        danger: {
                            50: 'var(--color-danger-50)',
                            100: 'var(--color-danger-100)',
                            500: 'var(--color-danger-500)',
                            600: 'var(--color-danger-600)',
                            700: 'var(--color-danger-700)',
                            900: 'var(--color-danger-900)',
                        },
                        neutral: {
                            50: 'var(--color-neutral-50)',
                            100: 'var(--color-neutral-100)',
                            200: 'var(--color-neutral-200)',
                            300: 'var(--color-neutral-300)',
                            400: 'var(--color-neutral-400)',
                            500: 'var(--color-neutral-500)',
                            600: 'var(--color-neutral-600)',
                            700: 'var(--color-neutral-700)',
                            800: 'var(--color-neutral-800)',
                            900: 'var(--color-neutral-900)',
                        }
                    },
                    fontFamily: {
                        sans: ['var(--font-sans)', 'sans-serif'],
                    },
                    borderRadius: {
                        sm: 'var(--radius-sm)',
                        md: 'var(--radius-md)',
                        lg: 'var(--radius-lg)',
                        xl: 'var(--radius-xl)',
                    },
                    boxShadow: {
                        xs: 'var(--shadow-xs)',
                        sm: 'var(--shadow-sm)',
                        md: 'var(--shadow-md)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js (via CDN) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Additional page styles -->
    @stack('styles')
</head>
<body class="bg-neutral-50" x-data="{ sidebarCollapsed: false, mobileSidebarOpen: false }">

    <!-- Sidebar Layout -->
    @include('layouts.sidebar')

    <!-- Main Content Wrapper -->
    <div class="main-content-wrapper" :class="{ 'sidebar-collapsed-wrapper': sidebarCollapsed }">
        <!-- Topbar -->
        @include('layouts.topbar')

        <!-- Content Area -->
        <main class="content-area">
            <!-- Stacking Toast Notifications -->
            <x-toast />

            <!-- Main Content Yield -->
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="d-flex align-center justify-between">
                <span class="text-xs text-muted">Sistem Informasi Monitoring Guru &copy; {{ date('Y') }} - Teknik Informatika ITATS</span>
                <span class="text-xs text-muted font-semibold">v1.0.0</span>
            </div>
        </footer>
    </div>

    <!-- Mobile Sidebar Backdrop -->
    <div class="sidebar-backdrop d-lg-none"
         x-show="mobileSidebarOpen"
         @click="mobileSidebarOpen = false"
         style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.4); z-index: 90; display: none;"
         x-transition:opacity>
    </div>

    <!-- Custom CSS for layout fixes that are cleaner outside utility layers -->
    <style>
        /* Collapsed wrapper adjustment */
        .sidebar-collapsed-wrapper {
            margin-left: 60px !important;
        }
        /* Backdrop responsive visibility */
        @media (min-width: 1025px) {
            .sidebar-backdrop {
                display: none !important;
            }
        }
        @media (max-width: 1024px) {
            .sidebar-backdrop {
                display: block;
            }
        }
    </style>

    <!-- Additional page scripts -->
    @stack('scripts')
</body>
</html>
