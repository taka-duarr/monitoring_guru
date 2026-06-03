@php
    $segments = request()->segments();
    $breadcrumbs = [];
    $accumulatedUrl = '';
    
    // Custom friendly names for segments
    $segmentNames = [
        'admin' => 'Admin',
        'dashboard' => 'Dashboard',
        'guru' => 'Manajemen Guru',
        'ketuakelas' => 'Ketua Kelas',
        'angkatan' => 'Angkatan',
        'kelas' => 'Kelas',
        'jurusan' => 'Jurusan',
        'mapel' => 'Mata Pelajaran',
        'ruangan' => 'Ruangan',
        'jadwalajar' => 'Jadwal Ajar',
        'absenmasuk' => 'Kehadiran Masuk',
        'absenkeluar' => 'Kehadiran Keluar',
        'izin' => 'Pengajuan Izin',
        'statuskelas' => 'Status Kelas',
        'users' => 'Manajemen Akun'
    ];

    // Query pending izin request dynamically
    try {
        $unreadIzins = \App\Models\Izin::with('jadwalAjar.guru')
            ->where('approval', false)
            ->latest()
            ->take(5)
            ->get();
        $unreadCount = $unreadIzins->count();
    } catch (\Exception $e) {
        $unreadIzins = collect();
        $unreadCount = 0;
    }
@endphp

<header class="topbar d-flex align-center justify-between px-4" style="height: var(--topbar-height); border-bottom: 1px solid var(--color-neutral-200); background-color: #ffffff; position: sticky; top: 0; z-index: 40;">
    <!-- Breadcrumb (Left Side) -->
    <div class="d-flex align-center gap-2">
        <!-- Desktop Sidebar Toggle -->
        <button class="btn btn-ghost btn-sm p-1 hidden md:flex items-center justify-center" @click="sidebarCollapsed = !sidebarCollapsed" aria-label="Toggle Sidebar" style="border-radius: var(--radius-md); width: 36px; height: 36px;">
            <i class="ti ti-menu-2" style="font-size: 20px;"></i>
        </button>

        <!-- Mobile Sidebar Toggle -->
        <button class="btn btn-ghost btn-sm p-1 flex md:hidden items-center justify-center" @click="mobileSidebarOpen = !mobileSidebarOpen" aria-label="Toggle Sidebar" style="border-radius: var(--radius-md); width: 36px; height: 36px;">
            <i class="ti ti-menu-2" style="font-size: 20px;"></i>
        </button>

        <nav class="breadcrumb-nav d-none d-sm-flex align-center text-sm" aria-label="Breadcrumb">
            <ol class="d-flex align-center gap-2" style="list-style: none;">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="text-muted font-medium hover:text-primary">Sistem</a>
                </li>
                @foreach($segments as $index => $segment)
                    @php
                        $accumulatedUrl .= '/' . $segment;
                        $isLast = ($index === count($segments) - 1);
                        $friendlyName = $segmentNames[strtolower($segment)] ?? ucwords(str_replace(['-', '_'], ' ', $segment));
                    @endphp
                    <li class="d-flex align-center gap-2">
                        <span class="text-muted" style="user-select: none;">/</span>
                        @if($isLast)
                            <span class="font-semibold text-primary" aria-current="page">{{ $friendlyName }}</span>
                        @else
                            <a href="{{ url($accumulatedUrl) }}" class="text-muted font-medium hover:text-primary">{{ $friendlyName }}</a>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    </div>

    <!-- Right Side Tools -->
    <div class="d-flex align-center gap-3">
        <!-- Notifications (Alpine.js Dropdown) -->
        <div class="position-relative" x-data="{ open: false }" @click.away="open = false">
            <button @click="open = !open" class="btn btn-ghost btn-sm p-2" aria-label="Notifikasi" style="position: relative; border-radius: var(--radius-full); width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                <span style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;">Notifikasi</span>
                <svg class="w-6 h-6 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <!-- Badge Count -->
                @if($unreadCount > 0)
                <span class="badge-count d-flex align-center justify-center font-bold" style="position: absolute; top: 2px; right: 2px; background-color: var(--color-danger-500); color: white; border-radius: 50%; font-size: 9px; width: 16px; height: 16px; border: 2px solid #ffffff;">
                    {{ $unreadCount }}
                </span>
                @endif
            </button>

            <!-- Notifications Dropdown Panel -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="card shadow-md p-3"
                 style="position: absolute; right: 0; top: 45px; width: 280px; z-index: 50; display: none;">
                <div class="d-flex align-center justify-between mb-2 pb-2" style="border-bottom: 1px solid var(--color-neutral-100);">
                    <span class="font-bold text-sm text-primary">Notifikasi</span>
                    @if($unreadCount > 0)
                    <a href="{{ route('izin.index') }}" class="text-xs font-semibold text-primary" style="text-decoration: none;">Tinjau Semua</a>
                    @endif
                </div>
                <div class="d-flex flex-column gap-2" style="max-height: 220px; overflow-y: auto;">
                    @forelse($unreadIzins as $izin)
                        <div class="py-2 px-1 text-xs" style="border-bottom: 1px solid var(--color-neutral-50);">
                            <a href="{{ route('izin.index') }}" style="text-decoration: none; display: block;" class="hover:text-primary">
                                <p class="mb-1 font-semibold text-neutral-800" style="margin: 0 0 2px 0;">Pengajuan Izin Baru</p>
                                <p class="text-muted mb-0" style="margin: 0; line-height: 1.3;">
                                    {{ $izin->jadwalAjar->guru->name ?? 'Seorang guru' }} mengajukan izin untuk tanggal {{ \Carbon\Carbon::parse($izin->tanggal_izin)->translatedFormat('d M Y') }}.
                                </p>
                            </a>
                        </div>
                    @empty
                        <div class="py-4 text-center text-xs text-neutral-400">
                            <i class="ti ti-bell-off" style="font-size: 24px; display: block; margin-bottom: 6px; color: var(--color-neutral-300);"></i>
                            Tidak ada notifikasi baru
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- User Profile Dropdown (Alpine.js) -->
        <div class="position-relative" x-data="{ open: false }" @click.away="open = false">
            <button @click="open = !open" class="d-flex align-center gap-2 p-1" style="background: transparent; border: none; cursor: pointer; border-radius: var(--radius-md); outline: none;">
                @php
                    $userName = Auth::user()->name ?? 'User';
                    $userInitials = collect(explode(' ', $userName))->map(fn($n) => substr($n, 0, 1))->take(2)->join('');
                    $colorHashIndex = (ord(substr($userName, 0, 1)) % 9) + 1;
                @endphp
                <div class="avatar avatar-md avatar-bg-{{ $colorHashIndex }}">
                    {{ $userInitials }}
                </div>
                <div class="text-left d-none d-sm-block">
                    <p class="mb-0 text-sm font-semibold" style="line-height: 1.2; color: var(--color-neutral-800);">{{ $userName }}</p>
                    <p class="mb-0 text-xs text-muted capitalize">{{ str_replace('_', ' ', Auth::user()->jabatan ?? 'Staf') }}</p>
                </div>
                <svg class="text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-left: 2px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="card shadow-md p-2"
                 style="position: absolute; right: 0; top: 50px; width: 180px; z-index: 50; display: none;">
                <div class="px-2 py-1 mb-1 text-xs text-muted" style="border-bottom: 1px solid var(--color-neutral-100);">
                    Kelola Akun
                </div>
                <a href="#" class="d-flex align-center gap-2 px-2 py-2 text-sm text-neutral-700 hover:bg-neutral-50 rounded" style="border-radius: var(--radius-sm); text-decoration: none; color: var(--color-neutral-700);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Profil Saya
                </a>
                <a href="#" class="d-flex align-center gap-2 px-2 py-2 text-sm text-neutral-700 hover:bg-neutral-50 rounded" style="border-radius: var(--radius-sm); text-decoration: none; color: var(--color-neutral-700);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Pengaturan
                </a>
                <div style="border-top: 1px solid var(--color-neutral-100); margin: 4px 0;"></div>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="d-flex align-center gap-2 px-2 py-2 text-sm text-danger hover:bg-danger-50 w-full" style="background: transparent; border: none; cursor: pointer; border-radius: var(--radius-sm); text-align: left; font-weight: 600; width: 100%;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
