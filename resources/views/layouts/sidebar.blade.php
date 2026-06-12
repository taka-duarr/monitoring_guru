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
            <span class="sidebar-brand-sub">SMAN X Surabaya</span>
        </div>
        <!-- Close button for mobile -->
        <button class="btn btn-ghost btn-sm p-1 d-md-none text-white ml-auto"
                @click="mobileSidebarOpen = false"
                aria-label="Close Sidebar"
                style="color: white !important;">
            <i class="ti ti-x" style="font-size: 20px;"></i>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <div class="sidebar-content">

        <!-- Grup: Utama -->
        <div class="sidebar-group-label" x-show="!sidebarCollapsed">Utama</div>
        <ul class="sidebar-menu-list">
            <!-- Dashboard -->
            <li class="sidebar-menu-item-wrapper">
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                   :class="{ 'active': {{ request()->routeIs('admin.dashboard') ? 'true' : 'false' }} }"
                   data-tooltip="Dashboard">
                    <i class="ti ti-layout-dashboard"></i>
                    <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Dashboard</span>
                </a>
            </li>

            <!-- Monitoring (Parent Submenu) -->
            <li class="sidebar-menu-item-wrapper" x-data="{ open: {{ (request()->routeIs('absenmasuk.*') || request()->routeIs('absenkeluar.*') || request()->routeIs('izin.*') || request()->routeIs('statuskelas.*')) ? 'true' : 'false' }} }">
                <div class="sidebar-menu-item {{ (request()->routeIs('absenmasuk.*') || request()->routeIs('absenkeluar.*') || request()->routeIs('izin.*') || request()->routeIs('statuskelas.*')) ? 'active' : '' }}"
                     @click="if (!sidebarCollapsed) { open = !open } else { sidebarCollapsed = false; open = true; }"
                     data-tooltip="Monitoring">
                    <i class="ti ti-eye"></i>
                    <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Monitoring</span>
                    <i class="ti ti-chevron-right sidebar-menu-arrow"
                       :class="{ 'rotated': open }"
                       x-show="!sidebarCollapsed"></i>
                </div>

                <!-- Submenu Items -->
                <ul class="sidebar-submenu"
                    x-show="open && !sidebarCollapsed"
                    x-collapse
                    style="display: none;">
                    <li>
                        <a href="{{ route('absenmasuk.index') }}"
                           class="sidebar-submenu-item {{ request()->routeIs('absenmasuk.index') ? 'active' : '' }}">
                            Rekap Kehadiran
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('izin.index') }}"
                           class="sidebar-submenu-item {{ request()->routeIs('izin.index') ? 'active' : '' }}">
                            Perizinan Guru
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('statuskelas.index') }}"
                           class="sidebar-submenu-item {{ request()->routeIs('statuskelas.index') ? 'active' : '' }}">
                            Status Kelas / Rekap
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Jadwal Mengajar -->
            <li class="sidebar-menu-item-wrapper">
                <a href="{{ route('jadwalajar.index') }}"
                   class="sidebar-menu-item {{ request()->routeIs('jadwalajar.*') ? 'active' : '' }}"
                   :class="{ 'active': {{ request()->routeIs('jadwalajar.*') ? 'true' : 'false' }} }"
                   data-tooltip="Jadwal Mengajar">
                    <i class="ti ti-calendar"></i>
                    <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Jadwal Mengajar</span>
                </a>
            </li>
        </ul>

        <!-- Grup: Data Master -->
        <div class="sidebar-group-label" x-show="!sidebarCollapsed">Data Master</div>
        <ul class="sidebar-menu-list">
            <!-- Data Guru -->
            <li class="sidebar-menu-item-wrapper">
                <a href="{{ route('guru.index') }}"
                   class="sidebar-menu-item {{ request()->routeIs('guru.*') ? 'active' : '' }}"
                   :class="{ 'active': {{ request()->routeIs('guru.*') ? 'true' : 'false' }} }"
                   data-tooltip="Data Guru">
                    <i class="ti ti-users"></i>
                    <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Data Guru</span>
                </a>
            </li>

            <!-- Angkatan -->
            <li class="sidebar-menu-item-wrapper">
                <a href="{{ route('angkatan.index') }}"
                   class="sidebar-menu-item {{ request()->routeIs('angkatan.*') ? 'active' : '' }}"
                   :class="{ 'active': {{ request()->routeIs('angkatan.*') ? 'true' : 'false' }} }"
                   data-tooltip="Angkatan">
                    <i class="ti ti-hash"></i>
                    <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Angkatan</span>
                </a>
            </li>

            <!-- Jurusan -->
            <li class="sidebar-menu-item-wrapper">
                <a href="{{ route('jurusan.index') }}"
                   class="sidebar-menu-item {{ request()->routeIs('jurusan.*') ? 'active' : '' }}"
                   :class="{ 'active': {{ request()->routeIs('jurusan.*') ? 'true' : 'false' }} }"
                   data-tooltip="Jurusan">
                    <i class="ti ti-git-branch"></i>
                    <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Jurusan</span>
                </a>
            </li>

            <!-- Mata Pelajaran -->
            <li class="sidebar-menu-item-wrapper">
                <a href="{{ route('mapel.index') }}"
                   class="sidebar-menu-item {{ request()->routeIs('mapel.*') ? 'active' : '' }}"
                   :class="{ 'active': {{ request()->routeIs('mapel.*') ? 'true' : 'false' }} }"
                   data-tooltip="Mata Pelajaran">
                    <i class="ti ti-book"></i>
                    <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Mata Pelajaran</span>
                </a>
            </li>

            <!-- Kelas -->
            <li class="sidebar-menu-item-wrapper">
                <a href="{{ route('kelas.index') }}"
                   class="sidebar-menu-item {{ request()->routeIs('kelas.*') ? 'active' : '' }}"
                   :class="{ 'active': {{ request()->routeIs('kelas.*') ? 'true' : 'false' }} }"
                   data-tooltip="Kelas">
                    <i class="ti ti-school"></i>
                    <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Kelas</span>
                </a>
            </li>

            <!-- Ruangan -->
            <li class="sidebar-menu-item-wrapper">
                <a href="{{ route('ruangan.index') }}"
                   class="sidebar-menu-item {{ request()->routeIs('ruangan.*') ? 'active' : '' }}"
                   :class="{ 'active': {{ request()->routeIs('ruangan.*') ? 'true' : 'false' }} }"
                   data-tooltip="Ruangan">
                    <i class="ti ti-door"></i>
                    <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Ruangan</span>
                </a>
            </li>

            <!-- Ketua Kelas -->
            <li class="sidebar-menu-item-wrapper">
                <a href="{{ route('ketuakelas.index') }}"
                   class="sidebar-menu-item {{ request()->routeIs('ketuakelas.*') ? 'active' : '' }}"
                   :class="{ 'active': {{ request()->routeIs('ketuakelas.*') ? 'true' : 'false' }} }"
                   data-tooltip="Ketua Kelas">
                    <i class="ti ti-crown"></i>
                    <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Ketua Kelas</span>
                </a>
            </li>
        </ul>

        <!-- Grup: Laporan -->
        <div class="sidebar-group-label" x-show="!sidebarCollapsed">Laporan</div>
        <ul class="sidebar-menu-list">
            <!-- Buat Laporan -->
            <li class="sidebar-menu-item-wrapper">
                <a href="{{ route('laporan.index') }}"
                   class="sidebar-menu-item {{ request()->routeIs('laporan.index') ? 'active' : '' }}"
                   :class="{ 'active': {{ request()->routeIs('laporan.index') ? 'true' : 'false' }} }"
                   data-tooltip="Buat Laporan">
                    <i class="ti ti-file-report"></i>
                    <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Buat Laporan</span>
                </a>
            </li>

            <!-- Riwayat Laporan -->
            <li class="sidebar-menu-item-wrapper">
                <a href="{{ route('laporan.riwayat') }}"
                   class="sidebar-menu-item {{ request()->routeIs('laporan.riwayat') ? 'active' : '' }}"
                   :class="{ 'active': {{ request()->routeIs('laporan.riwayat') ? 'true' : 'false' }} }"
                   data-tooltip="Riwayat Laporan">
                    <i class="ti ti-history"></i>
                    <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Riwayat Laporan</span>
                </a>
            </li>
        </ul>

        <!-- Grup: Sistem -->
        <div class="sidebar-group-label" x-show="!sidebarCollapsed">Sistem</div>
        <ul class="sidebar-menu-list">
            <!-- Kenaikan Kelas -->
            <li class="sidebar-menu-item-wrapper">
                <a href="{{ route('kenaikan_kelas.index') }}"
                   class="sidebar-menu-item {{ request()->routeIs('kenaikan_kelas.*') ? 'active' : '' }}"
                   :class="{ 'active': {{ request()->routeIs('kenaikan_kelas.*') ? 'true' : 'false' }} }"
                   data-tooltip="Kenaikan Kelas">
                    <i class="ti ti-arrow-up"></i>
                    <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Kenaikan Kelas</span>
                </a>
            </li>
            <!-- Pengaturan -->
            <li class="sidebar-menu-item-wrapper">
                <a href="{{ route('pengaturan.index') }}"
                   class="sidebar-menu-item {{ request()->routeIs('pengaturan.*') ? 'active' : '' }}"
                   :class="{ 'active': {{ request()->routeIs('pengaturan.*') ? 'true' : 'false' }} }"
                   data-tooltip="Pengaturan">
                    <i class="ti ti-settings"></i>
                    <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Pengaturan</span>
                </a>
            </li>

            <!-- Manajemen Akun -->
            <li class="sidebar-menu-item-wrapper">
                <a href="{{ route('users.index') }}"
                   class="sidebar-menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}"
                   :class="{ 'active': {{ request()->routeIs('users.*') ? 'true' : 'false' }} }"
                   data-tooltip="Manajemen Akun">
                    <i class="ti ti-user-circle"></i>
                    <span class="sidebar-menu-text" x-show="!sidebarCollapsed">Manajemen Akun</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        @php
            $adminUser = Auth::user();
            $adminName = $adminUser->name ?? 'Administrator';
            $adminInitials = collect(explode(' ', $adminName))->map(fn($n) => substr($n, 0, 1))->take(2)->join('');
            $adminRole = str_replace('_', ' ', $adminUser->jabatan ?? 'admin');
        @endphp

        <!-- Avatar -->
        <div class="sidebar-footer-avatar" title="{{ $adminName }}">
            {{ $adminInitials }}
        </div>

        <!-- Info Admin -->
        <div class="sidebar-footer-info" x-show="!sidebarCollapsed">
            <span class="sidebar-footer-name">{{ $adminName }}</span>
            <span class="sidebar-footer-role capitalize">{{ $adminRole }}</span>
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
