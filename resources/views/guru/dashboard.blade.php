@extends('layouts.guru')
@section('title', 'Jadwal Mengajar')

@section('content')
<div class="p-5">
    <div class="mb-6">
        <h2 class="text-2xl font-heading font-bold text-slate-800">Halo, {{ Auth::user()->name }}! 👋</h2>
        <p class="text-slate-500 mt-1">Berikut adalah jadwal mengajarmu hari ini.</p>
    </div>

    {{-- Data Ringkasan --}}
    @php
        $totalJadwal = count($jadwals);
        $selesaiJadwal = $jadwals->filter(fn($j) => $j->absen_masuk !== null && $j->absen_keluar !== null)->count();
        $belumJadwal = $jadwals->filter(fn($j) => $j->absen_masuk === null)->count();

        $nextJadwal = $jadwals->first(fn($j) => $j->absen_masuk === null);
        $nextJamMulai = $nextJadwal ? $nextJadwal->jam_mulai : null;

        // Pisahkan kelas aktif vs kelas lainnya
        $ongoingClasses = $jadwals->filter(fn($j) => $j->absen_masuk !== null && $j->absen_keluar === null);
        $otherClasses = $jadwals->filter(fn($j) => !($j->absen_masuk !== null && $j->absen_keluar === null));
    @endphp

    {{-- Panduan Penggunaan (Dismissible Card via Alpine.js) --}}
    <div x-data="{ showTips: true }" x-show="showTips" class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-4 flex items-start gap-3 relative transition-all" style="display: none;">
        <span class="text-xl flex-shrink-0">💡</span>
        <div class="flex-1 pr-6">
            <h4 class="font-bold text-sm text-blue-800">Tips Absensi Real-Time</h4>
            <p class="text-xs text-blue-700 mt-0.5">Mintalah Ketua Kelas menampilkan QR Code di ponselnya, lalu ketuk tombol **Scan QR MASUK / KELUAR** pada jadwal kelas yang sesuai untuk merekam kehadiran.</p>
        </div>
        <button @click="showTips = false" class="absolute top-3 right-3 text-blue-400 hover:text-blue-600 transition" aria-label="Close guide">
            <i class="ti ti-x text-sm"></i>
        </button>
    </div>

    {{-- Grid Panel Countdown & Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        {{-- Countdown Kelas Berikutnya --}}
        <div class="bg-brand-50 border border-brand-200 rounded-2xl p-4 flex flex-col justify-between">
            @if($nextJamMulai)
                <div>
                    <p class="text-xs font-semibold text-brand-600 uppercase tracking-wider mb-1">Kelas berikutnya</p>
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">⏰</span>
                        <div>
                            <p class="text-sm text-brand-700">Dimulai pukul <strong>{{ substr($nextJamMulai, 0, 5) }}</strong></p>
                            <p id="next-countdown" class="text-xl font-bold text-brand-800 tabular-nums">--:--:--</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3 py-2">
                    <span class="text-2xl">Class Clear ✅</span>
                    <p class="text-sm font-semibold text-brand-700">Tidak ada jadwal lagi hari ini</p>
                </div>
            @endif
        </div>

        {{-- Ringkasan Mengajar Hari Ini --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-4">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Ringkasan Mengajar Hari Ini</p>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                    <p class="text-xs text-slate-500 font-medium">Jadwal</p>
                    <p class="text-lg font-bold text-slate-800">{{ $totalJadwal }}</p>
                </div>
                <div class="bg-emerald-50 p-2.5 rounded-xl border border-emerald-100">
                    <p class="text-xs text-emerald-600 font-medium">Selesai</p>
                    <p class="text-lg font-bold text-emerald-700">{{ $selesaiJadwal }}</p>
                </div>
                <div class="bg-blue-50 p-2.5 rounded-xl border border-blue-100">
                    <p class="text-xs text-blue-600 font-medium">Belum</p>
                    <p class="text-lg font-bold text-blue-700">{{ $belumJadwal }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 1. SEKSI KELAS AKTIF (HIGH LIGHTED) --}}
    @if($ongoingClasses->isNotEmpty())
        <div class="mb-6">
            <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider mb-3">Kelas Sedang Berlangsung</h3>
            <div class="space-y-4">
                @foreach($ongoingClasses as $jadwal)
                    @php
                        $statusText = 'Sedang Mengajar';
                        $statusColor = 'bg-amber-500 text-white animate-pulse';
                        $borderClass = 'border-amber-400 ring-2 ring-amber-400/20';
                    @endphp
                    
                    <div class="bg-white rounded-2xl p-5 shadow-lg border-2 {{ $borderClass }} relative overflow-hidden transition-all duration-300 hover:shadow-xl">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500"></div>

                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="inline-block px-2.5 py-1 bg-brand-50 text-brand-700 text-[10px] font-bold uppercase tracking-wider rounded-md">{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</span>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $statusColor }} text-[10px] font-bold uppercase tracking-wider rounded-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                        {{ $statusText }}
                                    </span>
                                </div>
                                <h3 class="font-bold text-xl text-slate-800 leading-tight">
                                    <a href="{{ route('guru.riwayat_mapel', $jadwal->mapel_id) }}" class="hover:text-brand-600 hover:underline transition">
                                        {{ $jadwal->mapel->name ?? 'Mata Pelajaran' }}
                                    </a>
                                </h3>
                                <p class="text-sm text-slate-500 mt-1">Kelas <strong class="text-slate-700">{{ $jadwal->kelas->name ?? '-' }}</strong> • {{ $jadwal->ruangan->name ?? 'Ruang Belum Diset' }}</p>
                            </div>
                        </div>

                        <!-- Informasi Rekaman Absensi -->
                        <div class="flex items-center gap-4 mt-4 pt-4 border-t border-slate-100 text-sm">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                <span class="text-slate-600 font-medium">Masuk: <strong class="text-slate-800">{{ $jadwal->absen_masuk->jam_masuk }}</strong></span>
                            </div>
                            <div class="flex items-center gap-1.5 opacity-70">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-slate-500">Belum Keluar</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('guru.scan') }}"
                               class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-center py-3 rounded-xl text-sm font-semibold transition shadow-md shadow-amber-500/20 min-h-[44px] flex items-center justify-center gap-2 active:scale-[0.98]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Scan QR KELUAR
                            </a>
                            <a href="{{ route('guru.absen_murid', $jadwal->absen_masuk->id) }}"
                               class="flex-1 bg-brand-500 hover:bg-brand-600 text-white text-center py-3 rounded-xl text-sm font-semibold transition shadow-md shadow-brand-500/20 min-h-[44px] flex items-center justify-center gap-2 active:scale-[0.98]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Absen Murid
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- 2. SEKSI JADWAL LAINNYA --}}
    <div>
        <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider mb-3">Jadwal Mengajar Hari Ini</h3>
        <div class="space-y-4">
            @forelse($otherClasses as $jadwal)
                @php
                    $hasMasuk = $jadwal->absen_masuk !== null;
                    $hasKeluar = $jadwal->absen_keluar !== null;

                    // Cek apakah sekarang sudah >= jam_mulai (toleransi 0 menit)
                    $jamMulaiCarbon = \Carbon\Carbon::createFromFormat('H:i', substr($jadwal->jam_mulai, 0, 5));
                    $isTimeToScan = \Carbon\Carbon::now()->gte($jamMulaiCarbon);

                    // Tentukan status dan warna
                    if ($hasMasuk && $hasKeluar) {
                        $statusText = 'Selesai';
                        $statusColor = 'bg-emerald-100 text-emerald-700';
                        $borderClass = 'border-emerald-200';
                        $canScan = false;
                    } else {
                        // Belum absen
                        $statusText = 'Belum Absen';
                        $statusColor = 'bg-blue-100 text-blue-700';
                        $borderClass = 'border-blue-200';
                        $canScan = true;
                    }
                @endphp

                <div class="bg-white rounded-2xl p-5 shadow-sm border {{ $borderClass }} relative overflow-hidden transition-all hover:shadow-md">
                    @if(!$hasMasuk)
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-500"></div>
                    @elseif($hasMasuk && $hasKeluar)
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-500"></div>
                    @endif

                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-block px-2.5 py-1 bg-brand-50 text-brand-700 text-[10px] font-bold uppercase tracking-wider rounded-md">{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</span>
                                <span class="inline-block px-2.5 py-1 {{ $statusColor }} text-[10px] font-bold uppercase tracking-wider rounded-md">{{ $statusText }}</span>
                            </div>
                            <h3 class="font-bold text-lg text-slate-800 leading-tight">
                                <a href="{{ route('guru.riwayat_mapel', $jadwal->mapel_id) }}" class="hover:text-brand-600 hover:underline transition">
                                    {{ $jadwal->mapel->name ?? 'Mata Pelajaran' }}
                                </a>
                            </h3>
                            <p class="text-sm text-slate-500 mt-1">Kelas {{ $jadwal->kelas->name ?? '-' }} • {{ $jadwal->ruangan->name ?? 'Ruang Belum Diset' }}</p>
                        </div>
                    </div>

                    <!-- Informasi Rekaman Absensi -->
                    @if($hasMasuk)
                        <div class="flex items-center gap-4 mt-4 pt-4 border-t border-slate-100 text-sm">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                <span class="text-slate-600 font-medium">Masuk: <strong class="text-slate-800">{{ $jadwal->absen_masuk->jam_masuk }}</strong></span>
                            </div>

                            @if($hasKeluar)
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013 3h4a3 3 0 013 3v1"></path></svg>
                                    <span class="text-slate-600 font-medium">Keluar: <strong class="text-slate-800">{{ $jadwal->absen_keluar->jam_keluar }}</strong></span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    @if($canScan)
                        <div class="mt-4 flex gap-2">
                            @if($isTimeToScan)
                                {{-- Sudah waktunya: tampilkan tombol scan masuk normal --}}
                                <a href="{{ route('guru.scan') }}"
                                   class="flex-1 bg-brand-500 hover:bg-brand-600 text-white text-center py-3 rounded-xl text-sm font-semibold transition shadow-sm min-h-[44px] flex items-center justify-center gap-2 active:scale-[0.98]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Scan QR MASUK
                                </a>
                            @else
                                {{-- Belum waktunya: tampilkan tombol terkunci dengan countdown --}}
                                <div class="flex-1 relative" data-unlock-time="{{ $jadwal->jam_mulai }}">
                                    <div class="w-full bg-slate-100 border border-slate-200 text-slate-400 text-center py-3 rounded-xl text-sm font-semibold cursor-not-allowed select-none min-h-[44px] flex items-center justify-center gap-2 scan-locked-btn">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        <span>Terbuka pukul <strong class="text-slate-500">{{ substr($jadwal->jam_mulai, 0, 5) }}</strong>
                                            &nbsp;(<span class="scan-unlock-countdown text-xs font-mono text-slate-500">--:--</span>)
                                        </span>
                                    </div>
                                    {{-- Link tersembunyi, akan ditampilkan JS saat waktunya tiba --}}
                                    <a href="{{ route('guru.scan') }}"
                                       class="scan-unlocked-btn w-full bg-brand-500 hover:bg-brand-600 text-white text-center py-3 rounded-xl text-sm font-semibold transition shadow-sm min-h-[44px] flex items-center justify-center gap-2 active:scale-[0.98]"
                                       style="display:none;">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Scan QR MASUK
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                @if($ongoingClasses->isEmpty())
                    <div class="bg-white rounded-2xl p-8 text-center shadow-sm border border-slate-100">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-slate-500 font-medium">Hore! Tidak ada jadwal mengajar di hari {{ $hariIni }} ini.</p>
                    </div>
                @endif
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    // --- Countdown card ke jadwal berikutnya ---
    var targetTime = @json($nextJamMulai);
    var el = document.getElementById('next-countdown');
    if (targetTime && el) {
        function tickCountdown() {
            var now = new Date();
            var parts = targetTime.split(':');
            var target = new Date();
            target.setHours(parseInt(parts[0], 10), parseInt(parts[1], 10), parseInt(parts[2] || 0, 10), 0);
            var diff = Math.floor((target - now) / 1000);
            if (diff <= 0) {
                el.textContent = '00:00:00';
                el.closest('.bg-brand-50').querySelector('p.text-sm').textContent = 'Kelas dimulai sekarang!';
                return;
            }
            var h = Math.floor(diff / 3600);
            var m = Math.floor((diff % 3600) / 60);
            var s = diff % 60;
            el.textContent =
                String(h).padStart(2, '0') + ':' +
                String(m).padStart(2, '0') + ':' +
                String(s).padStart(2, '0');
        }
        tickCountdown();
        setInterval(tickCountdown, 1000);
    }

    // --- Unlock tombol Scan QR MASUK secara real-time ---
    var lockedContainers = document.querySelectorAll('[data-unlock-time]');
    if (lockedContainers.length === 0) return;

    function padTwo(n) { return String(n).padStart(2, '0'); }

    function checkUnlock() {
        var now = new Date();
        lockedContainers.forEach(function (container) {
            var unlockTime = container.getAttribute('data-unlock-time');
            var parts = unlockTime.split(':');
            var target = new Date();
            target.setHours(parseInt(parts[0], 10), parseInt(parts[1], 10), 0, 0);

            var diff = Math.floor((target - now) / 1000);

            var lockedBtn  = container.querySelector('.scan-locked-btn');
            var unlockedBtn = container.querySelector('.scan-unlocked-btn');
            var countdownEl = container.querySelector('.scan-unlock-countdown');

            if (diff <= 0) {
                // Waktunya tiba — tampilkan tombol asli, sembunyikan terkunci
                if (lockedBtn)   { lockedBtn.style.display = 'none'; }
                if (unlockedBtn) { unlockedBtn.style.display = 'flex'; }
            } else {
                // Perbarui countdown di dalam tombol terkunci
                if (countdownEl) {
                    var mm = Math.floor(diff / 60);
                    var ss = diff % 60;
                    countdownEl.textContent = padTwo(mm) + ':' + padTwo(ss);
                }
            }
        });
    }

    checkUnlock();
    setInterval(checkUnlock, 1000);
})();
</script>
@endpush
