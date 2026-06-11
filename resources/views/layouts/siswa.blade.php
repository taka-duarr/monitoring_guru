@php
    $notifications = [];
    $unreadCount = 0;
    if (Auth::check()) {
        $user = Auth::user();
        $kelas = $user->kelas;

        if ($kelas) {
            $now = \Carbon\Carbon::now();
            $todayDate = \Carbon\Carbon::today()->toDateString();
            $hariIni = $now->locale('id')->isoFormat('dddd');

            // 1. Get schedules for today
            $jadwals = \App\Models\JadwalAjar::with(['mapel', 'guru'])
                ->where('kelas_id', $kelas->id)
                ->where('hari', $hariIni)
                ->get();

            foreach ($jadwals as $jadwal) {
                $timeParts = explode(':', $jadwal->jam_mulai);
                $jamMulai = \Carbon\Carbon::today()->setHour((int)$timeParts[0])->setMinute((int)$timeParts[1])->setSecond(0);
                $diffInMinutes = $now->diffInMinutes($jamMulai, false);

                // If class starts in 0 to 10 minutes
                if ($diffInMinutes >= 0 && $diffInMinutes <= 10) {
                    // Check if teacher has approved permission for this schedule today
                    $izin = \App\Models\Izin::where('jadwal_ajar_id', $jadwal->id)
                        ->where('tanggal_izin', $todayDate)
                        ->where('approval', true)
                        ->first();

                    if ($izin) {
                        $notifications[] = [
                            'id' => 'siswa_izin_' . $izin->id . '_' . $todayDate,
                            'title' => 'Kelas Kosong (Izin)',
                            'content' => 'Pelajaran ' . ($jadwal->mapel->name ?? 'Mapel') . ' pukul ' . substr($jadwal->jam_mulai, 0, 5) . ' kosong karena Guru ' . ($jadwal->guru->name ?? 'Guru') . ' sedang izin.',
                            'time' => $diffInMinutes . ' mnt lagi',
                            'type' => 'danger'
                        ];
                        $unreadCount++;
                    } else {
                        // Check if teacher checked in
                        $hasAbsen = \App\Models\AbsenMasuk::where('jadwal_ajar_id', $jadwal->id)
                            ->where('tanggal', $todayDate)
                            ->exists();

                        if (!$hasAbsen) {
                            $notifications[] = [
                                'id' => 'siswa_jadwal_' . $jadwal->id . '_' . $todayDate,
                                'title' => 'Pelajaran 10 Menit Lagi',
                                'content' => 'Pelajaran ' . ($jadwal->mapel->name ?? 'Mapel') . ' oleh Guru ' . ($jadwal->guru->name ?? 'Guru') . ' akan dimulai pukul ' . substr($jadwal->jam_mulai, 0, 5),
                                'time' => $diffInMinutes . ' mnt lagi',
                                'type' => 'warning'
                            ];
                            $unreadCount++;
                        }
                    }
                }
            }

            // 2. Also show other general absent teachers today for this class
            $todayIzins = \App\Models\Izin::with(['jadwalAjar.guru', 'jadwalAjar.mapel'])
                ->where('tanggal_izin', $todayDate)
                ->where('approval', true)
                ->whereHas('jadwalAjar', function ($query) use ($kelas) {
                    $query->where('kelas_id', $kelas->id);
                })
                ->get();

            foreach ($todayIzins as $izin) {
                // To avoid duplication with the 10-minute warning
                $timeParts = explode(':', $izin->jadwalAjar->jam_mulai);
                $jamMulai = \Carbon\Carbon::today()->setHour((int)$timeParts[0])->setMinute((int)$timeParts[1])->setSecond(0);
                $diffInMinutes = $now->diffInMinutes($jamMulai, false);

                if (!($diffInMinutes >= 0 && $diffInMinutes <= 10)) {
                    $notifications[] = [
                        'id' => 'siswa_izin_general_' . $izin->id . '_' . $todayDate,
                        'title' => 'Guru Tidak Hadir',
                        'content' => 'Guru ' . ($izin->jadwalAjar->guru->name ?? 'Guru') . ' (Mapel ' . ($izin->jadwalAjar->mapel->name ?? 'Mapel') . ') tidak hadir hari ini.',
                        'time' => 'Hari ini',
                        'type' => 'danger'
                    ];
                }
            }
        }
    }
    $unreadCount = $unreadCount ?? 0;
    $notifications = $notifications ?? [];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2563eb">
    <title>@yield('title', 'Portal Siswa')</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
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
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
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
        <div class="flex items-center gap-3">
            @php
                $user = Auth::user();
                $userName = $user->name ?? 'Siswa';
                $userInitials = collect(explode(' ', $userName))->map(fn($n) => substr($n, 0, 1))->take(2)->join('');
            @endphp
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white font-bold overflow-hidden shadow-inner flex-shrink-0">
                @if($user->foto && file_exists(public_path('storage/' . $user->foto)))
                    <img src="{{ asset('storage/' . $user->foto) }}" alt="{{ $userName }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper($userInitials) }}
                @endif
            </div>
            <div>
                <h1 class="font-heading font-bold text-white-800 text-xl tracking-tight leading-none">Selamat Datang</h1>
                <p class="text-[10px] text-brand-100 opacity-90 mt-0.5">{{ $userName }} (Ketua Kelas)</p>
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

            @if(request()->routeIs('siswa.profile.index'))
                <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-white/20 hover:bg-white/10 text-sm font-medium transition-colors text-white" style="text-decoration: none;">
                    <i class="ti ti-smart-home" style="font-size: 16px;"></i> Ruang Kelas
                </a>
            @else
                <a href="{{ route('siswa.profile.index') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-white/20 hover:bg-white/10 text-sm font-medium transition-colors text-white" style="text-decoration: none;">
                    <i class="ti ti-user" style="font-size: 16px;"></i> Profil Saya
                </a>
            @endif
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white active:bg-white/30 transition-colors backdrop-blur-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto relative p-5">
        <x-toast />

        @yield('content')
    </main>

</body>
</html>
