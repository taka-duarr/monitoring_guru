@php
    $notifications = [];
    $unreadCount = 0;
    if (Auth::check()) {
        $now = \Carbon\Carbon::now();
        $todayDate = \Carbon\Carbon::today()->toDateString();
        $hariIni = $now->locale('id')->isoFormat('dddd');

        // 1. Get schedules for today
        $jadwals = \App\Models\JadwalAjar::with(['mapel', 'kelas'])
            ->where('guru_id', Auth::id())
            ->where('hari', $hariIni)
            ->get();

        foreach ($jadwals as $jadwal) {
            $timeParts = explode(':', $jadwal->jam_mulai);
            $jamMulai = \Carbon\Carbon::today()->setHour((int)$timeParts[0])->setMinute((int)$timeParts[1])->setSecond(0);
            $diffInMinutes = $now->diffInMinutes($jamMulai, false);

            // If it starts in 0 to 10 minutes
            if ($diffInMinutes >= 0 && $diffInMinutes <= 10) {
                // Check if already checked in
                $hasAbsen = \App\Models\AbsenMasuk::where('jadwal_ajar_id', $jadwal->id)
                    ->where('tanggal', $todayDate)
                    ->exists();

                if (!$hasAbsen) {
                    $notifications[] = [
                        'id' => 'guru_jadwal_' . $jadwal->id . '_' . $todayDate,
                        'title' => 'Mengajar 10 Menit Lagi',
                        'content' => 'Jadwal ' . ($jadwal->mapel->name ?? 'Mapel') . ' di kelas ' . ($jadwal->kelas->name ?? 'Kelas') . ' akan dimulai pukul ' . substr($jadwal->jam_mulai, 0, 5),
                        'time' => $diffInMinutes . ' mnt lagi',
                        'type' => 'warning'
                    ];
                    $unreadCount++;
                }
            }
        }

        // 2. Get status updates of recent permission requests (last 5)
        $izins = \App\Models\Izin::with('jadwalAjar.mapel')
            ->where('guru_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();

        foreach ($izins as $izin) {
            if ($izin->approval) {
                // Approved permission
                $notifications[] = [
                    'id' => 'guru_izin_' . $izin->id . '_approved',
                    'title' => 'Izin Disetujui',
                    'content' => 'Pengajuan izin untuk tanggal ' . \Carbon\Carbon::parse($izin->tanggal_izin)->translatedFormat('d M Y') . ' (' . ($izin->jadwalAjar->mapel->name ?? 'Mapel') . ') telah disetujui.',
                    'time' => $izin->updated_at->diffForHumans(),
                    'type' => 'success'
                ];
            } else {
                // Pending permission
                $notifications[] = [
                    'id' => 'guru_izin_' . $izin->id . '_pending',
                    'title' => 'Izin Diajukan',
                    'content' => 'Pengajuan izin untuk tanggal ' . \Carbon\Carbon::parse($izin->tanggal_izin)->translatedFormat('d M Y') . ' sedang menunggu persetujuan.',
                    'time' => $izin->created_at->diffForHumans(),
                    'type' => 'info'
                ];
            }
        }
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1B2F4E">
    <title>@yield('title', 'Portal Guru')</title>
    @php
        $appLogo = \App\Models\Setting::get('school_logo') && file_exists(public_path('storage/' . \App\Models\Setting::get('school_logo'))) 
            ? asset('storage/' . \App\Models\Setting::get('school_logo')) 
            : asset('favicon.ico');
    @endphp
    <link rel="icon" href="{{ $appLogo }}">
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-neutral-50 font-sans text-slate-800 antialiased" x-data="{ sidebarCollapsed: false, mobileSidebarOpen: false }">

    <!-- Sidebar Container -->
    <aside class="sidebar"
           :class="{ 'sidebar-collapsed': sidebarCollapsed, 'sidebar-open': mobileSidebarOpen }">

        <!-- Logo & Nama Sistem -->
        <div class="sidebar-brand-wrapper">
            <div class="sidebar-logo-icon" title="SIMGURU">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
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

                <!-- Profil Saya -->
                <li class="sidebar-menu-item-wrapper">
                    <a href="{{ route('guru.profile.index') }}"
                       class="sidebar-menu-item {{ request()->routeIs('guru.profile.index') ? 'active' : '' }}"
                       :class="{ 'active': {{ request()->routeIs('guru.profile.index') ? 'true' : 'false' }} }"
                       data-tooltip="Profil Saya">
                        <i class="ti ti-user"></i>
                        <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Profil Saya</span>
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
            <div class="sidebar-footer-avatar" title="{{ $userName }}" style="overflow: hidden; display: flex; align-items: center; justify-center: center;">
                @if($user->foto && file_exists(public_path('storage/' . $user->foto)))
                    <img src="{{ asset('storage/' . $user->foto) }}" alt="{{ $userName }}" class="w-full h-full object-cover">
                @else
                    {{ $userInitials }}
                @endif
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
                    <h1 class="font-heading font-bold text-xl tracking-tight leading-none text-white" style="color: white !important;">Selamat datang</h1>
                    <p class="text-[10px] text-brand-100 opacity-90 mt-0.5">{{ Auth::user()->name }} (Guru)</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Notifications Dropdown (Alpine.js) -->
                <div class="relative" x-data="{
                    open: false,
                    dismissedNotifs: JSON.parse(localStorage.getItem('dismissed_notifications') || '[]'),
                    notifications: {{ json_encode($notifications) }},
                    dismiss(id) {
                        this.dismissedNotifs.push(id);
                        localStorage.setItem('dismissed_notifications', JSON.stringify(this.dismissedNotifs));
                    },
                    dismissAll() {
                        this.notifications.forEach(n => {
                            if (!this.dismissedNotifs.includes(n.id)) {
                                this.dismissedNotifs.push(n.id);
                            }
                        });
                        localStorage.setItem('dismissed_notifications', JSON.stringify(this.dismissedNotifs));
                    },
                    isDismissed(id) {
                        return this.dismissedNotifs.includes(id);
                    },
                    get activeNotifications() {
                        return this.notifications.filter(n => !this.isDismissed(n.id));
                    },
                    get activeCount() {
                        return this.notifications.filter(n => !this.isDismissed(n.id) && (n.type === 'warning' || n.type === 'danger')).length;
                    }
                }" @click.away="open = false">
                    <button @click="open = !open" class="relative w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white hover:bg-white/30 transition active:scale-95" aria-label="Notifikasi">
                        <i class="ti ti-bell text-lg"></i>
                        <!-- Badge Count -->
                        <template x-if="activeCount > 0">
                            <span class="absolute top-1.5 right-1.5 w-4.5 h-4.5 bg-red-500 text-white rounded-full text-[9px] font-bold flex items-center justify-center border-2 border-brand-600 shadow-sm px-1 leading-none" x-text="activeCount"></span>
                        </template>
                    </button>

                    <!-- Notifications Dropdown Panel -->
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 p-4"
                         style="display: none; text-align: left;">
                        <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                            <span class="font-bold text-sm text-slate-800">Notifikasi</span>
                            <template x-if="activeNotifications.length > 0">
                                <button @click="dismissAll()" class="text-xs font-semibold text-brand-600 hover:text-brand-800 transition" style="background: none; border: none; padding: 0; cursor: pointer;">Bersihkan Semua</button>
                            </template>
                        </div>
                        <div class="flex flex-col gap-3 max-h-72 overflow-y-auto pr-1">
                            <!-- List Notifications -->
                            <template x-for="notif in activeNotifications" :key="notif.id">
                                <div class="flex gap-2.5 p-2 rounded-xl hover:bg-slate-50 transition relative group">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                         :class="{
                                             'bg-red-50 text-red-600': notif.type === 'danger',
                                             'bg-amber-50 text-amber-600': notif.type === 'warning',
                                             'bg-emerald-50 text-emerald-600': notif.type === 'success',
                                             'bg-blue-50 text-blue-600': notif.type !== 'danger' && notif.type !== 'warning' && notif.type !== 'success'
                                         }">
                                        <template x-if="notif.type === 'danger'">
                                            <i class="ti ti-alert-triangle text-base"></i>
                                        </template>
                                        <template x-if="notif.type === 'warning'">
                                            <i class="ti ti-bell-ringing text-base"></i>
                                        </template>
                                        <template x-if="notif.type === 'success'">
                                            <i class="ti ti-circle-check text-base"></i>
                                        </template>
                                        <template x-if="notif.type !== 'danger' && notif.type !== 'warning' && notif.type !== 'success'">
                                            <i class="ti ti-info-circle text-base"></i>
                                        </template>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-slate-800 leading-tight" x-text="notif.title"></p>
                                        <p class="text-[11px] text-slate-500 mt-1 leading-normal break-words" x-text="notif.content"></p>
                                        <span class="text-[10px] text-slate-400 font-semibold block mt-1.5" x-text="notif.time"></span>
                                    </div>
                                    <!-- Dismiss Button -->
                                    <button @click.stop="dismiss(notif.id)" class="text-slate-400 hover:text-slate-600 transition flex-shrink-0 self-start p-1 rounded hover:bg-slate-100" title="Hapus">
                                        <i class="ti ti-x text-xs"></i>
                                    </button>
                                </div>
                            </template>
                            
                            <!-- Empty State -->
                            <template x-if="activeNotifications.length === 0">
                                <div class="py-6 text-center text-xs text-slate-400">
                                    <i class="ti ti-bell-off text-2xl block mb-2 text-slate-300"></i>
                                    Tidak ada notifikasi baru
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white hover:bg-white/30 transition-colors backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Content -->
        <main class="content-area @yield('content_class')">
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
