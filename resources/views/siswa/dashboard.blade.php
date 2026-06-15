@extends('layouts.siswa')
@section('title', 'Dasbor Kelas')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-heading font-bold text-slate-800">Ruang Kelas</h2>
    <p class="text-slate-500 mt-1">
        @if($kelas)
            Kamu adalah Ketua untuk kelas <strong class="text-brand-600">{{ $kelas->name }}</strong>
        @else
            Kamu belum ditugaskan ke kelas manapun.
        @endif
    </p>
</div>

@if($kelas && !$kelas->is_active)
    <div class="bg-warning-50 border border-warning-200 rounded-3xl p-8 text-center shadow-sm">
        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-warning-500 mb-4 mx-auto shadow-sm">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h3 class="font-bold text-slate-800 text-lg mb-2">Kelas Telah Lulus / Nonaktif</h3>
        <p class="text-slate-500 text-sm max-w-md mx-auto">
            Kelas <strong>{{ $kelas->name }}</strong> sudah tidak aktif. Data absen tidak dapat ditampilkan.
        </p>
    </div>
@elseif($kelas && $kelas->is_active)
    {{-- Data Ringkasan --}}
    @php
        $totalJadwal = count($allJadwals);
        $selesaiJadwal = $allJadwals->filter(fn($j) => $j->absen_masuk !== null && $j->absen_keluar !== null)->count();
        $belumJadwal = $allJadwals->filter(fn($j) => $j->absen_masuk === null)->count();

        $nextJadwal = $allJadwals->first(fn($j) => $j->absen_masuk === null);
        $nextJamMulai = $nextJadwal ? $nextJadwal->jam_mulai : null;

        // Pisahkan kelas aktif vs kelas lainnya
        $ongoingClasses = $allJadwals->filter(fn($j) => $j->absen_masuk !== null && $j->absen_keluar === null);
        $otherClasses = $jadwals->filter(fn($j) => !($j->absen_masuk !== null && $j->absen_keluar === null));
    @endphp

    {{-- Panduan Penggunaan (Dismissible Card via Alpine.js) --}}
    <div x-data="{ showTips: true }" x-show="showTips" class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-4 flex items-start gap-3 relative transition-all" style="display: none;">
        <span class="text-xl flex-shrink-0">💡</span>
        <div class="flex-1 pr-6">
            <h4 class="font-bold text-sm text-blue-800">Panduan Presensi Kelas</h4>
            <p class="text-xs text-blue-700 mt-0.5">Ketuk tombol **Generate QR MASUK / KELUAR** pada jadwal kelas aktif, kemudian tunjukkan kode QR tersebut pada Guru agar dapat dipindai untuk mencatat kehadiran Guru.</p>
        </div>
        <button @click="showTips = false" class="absolute top-3 right-3 text-blue-400 hover:text-blue-600 transition" aria-label="Close guide">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
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
                            <p id="next-class-countdown" class="text-xl font-bold text-brand-800 tabular-nums">--:--:--</p>
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

        {{-- Ringkasan Kehadiran Guru Hari Ini --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-4">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Ringkasan Kehadiran Guru Hari Ini</p>
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($ongoingClasses as $jadwal)
                    @php
                        $statusText = 'Sedang Belajar';
                        $statusColor = 'bg-amber-500 text-white animate-pulse';
                        $borderClass = 'border-amber-400 ring-2 ring-amber-400/20';
                    @endphp
                    
                    <div class="bg-white rounded-2xl p-5 shadow-lg border-2 {{ $borderClass }} relative overflow-hidden transition-all duration-300 hover:shadow-xl flex flex-col justify-between h-full">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500"></div>

                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="inline-block px-2.5 py-1 bg-brand-50 text-brand-700 text-[10px] font-bold uppercase tracking-wider rounded-md">{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</span>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $statusColor }} text-[10px] font-bold uppercase tracking-wider rounded-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                        {{ $statusText }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-xl text-slate-800 leading-tight">{{ $jadwal->mapel->name ?? 'Mapel Tidak Diketahui' }}</h4>
                                <p class="text-sm text-slate-500 mt-1">Guru: <strong class="text-slate-700">{{ $jadwal->guru->name ?? '-' }}</strong> • Ruang: {{ $jadwal->ruangan->name ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button onclick="showQrModal('{{ $jadwal->id }}', '{{ $jadwal->mapel->name ?? 'Mapel' }}', 'keluar')" class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold py-3 px-6 rounded-xl shadow-md shadow-amber-500/20 transition active:scale-[0.98] w-full">
                                Generate QR KELUAR
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- 2. SEKSI JADWAL LAINNYA --}}
    <div>
        <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider mb-3">Jadwal Kelas Hari Ini</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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
                        $canGenerate = false;
                    } else {
                        $statusText = 'Belum Absen';
                        $statusColor = 'bg-blue-100 text-blue-700';
                        $borderClass = 'border-blue-200';
                        $canGenerate = true;
                    }
                @endphp
                <div class="bg-white rounded-2xl p-5 shadow-sm border {{ $borderClass }} relative overflow-hidden transition-all hover:shadow-md flex flex-col justify-between h-full">
                    @if(!$hasMasuk)
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-500"></div>
                    @elseif($hasMasuk && $hasKeluar)
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-500"></div>
                    @endif
                    
                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                               <span class="inline-block px-2.5 py-1 bg-brand-50 text-brand-700 text-[10px] font-bold uppercase tracking-wider rounded-md">{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</span>
                               <span class="inline-block px-2.5 py-1 {{ $statusColor }} text-[10px] font-bold uppercase tracking-wider rounded-md">{{ $statusText }}</span>
                            </div>
                            <h4 class="font-bold text-lg text-slate-800 leading-tight">{{ $jadwal->mapel->name ?? 'Mapel Tidak Diketahui' }}</h4>
                            <p class="text-sm text-slate-500 mt-1">Guru: {{ $jadwal->guru->name ?? '-' }} • Ruang: {{ $jadwal->ruangan->name ?? '-' }}</p>
                        </div>
                    </div>
                   
                    <div class="mt-4">
                        @if($canGenerate)
                            @if($isTimeToScan)
                                {{-- Generate QR untuk MASUK --}}
                                <button onclick="showQrModal('{{ $jadwal->id }}', '{{ $jadwal->mapel->name ?? 'Mapel' }}', 'masuk')" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-md shadow-brand-500/20 transition active:scale-[0.98]">
                                    Generate QR MASUK
                                </button>
                            @else
                                {{-- Belum jam mengajar, tombol locked dengan countdown --}}
                                <div class="relative w-full" data-unlock-time="{{ $jadwal->jam_mulai }}">
                                    <div class="w-full bg-slate-100 border border-slate-200 text-slate-400 text-center py-2.5 px-5 rounded-xl text-sm font-semibold cursor-not-allowed select-none flex items-center justify-center gap-2 qr-locked-btn">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        <span>Terbuka <strong class="text-slate-500">{{ substr($jadwal->jam_mulai, 0, 5) }}</strong>
                                            &nbsp;(<span class="qr-unlock-countdown text-xs font-mono text-slate-500">--:--</span>)
                                        </span>
                                    </div>
                                    {{-- Button tersembunyi, akan ditampilkan JS saat waktunya tiba --}}
                                    <button onclick="showQrModal('{{ $jadwal->id }}', '{{ $jadwal->mapel->name ?? 'Mapel' }}', 'masuk')"
                                            class="qr-unlocked-btn w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-md shadow-brand-500/20 transition active:scale-[0.98]"
                                            style="display:none;">
                                        Generate QR MASUK
                                    </button>
                                </div>
                            @endif
                        @else
                            {{-- Selesai --}}
                            <div class="text-emerald-600 font-bold flex items-center justify-center gap-1.5 py-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Selesai</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                @if($ongoingClasses->isEmpty())
                    <div class="bg-white rounded-3xl p-8 text-center shadow-sm border border-slate-100 col-span-full">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 mb-4 mx-auto">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="font-bold text-slate-700">Tidak ada jadwal hari ini</h3>
                        <p class="text-slate-500 text-sm mt-1">Asyik, kelasmu sedang kosong hari ini!</p>
                    </div>
                @endif
            @endforelse
        </div>
        <!-- Pagination Links -->
        <div class="mt-6 flex justify-center">
            {{ $jadwals->links() }}
        </div>
    </div>

    <!-- Modal QR Code -->
    <div id="qr-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeQrModal()"></div>

        <!-- Modal Content -->
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-sm relative z-10 overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="qr-modal-content">
            <div class="p-6 text-center flex flex-col items-center justify-center h-full">
                <h3 id="qr-mapel-name" class="font-bold text-xl text-slate-800 mb-1">Nama Mapel</h3>
                <p class="text-xs text-slate-500 mb-6">Tunjukkan QR ini ke Guru yang bersangkutan</p>

                <div class="flex justify-center mb-4">
                    <div id="qr-container" class="p-4 bg-white border-4 border-slate-100 rounded-2xl shadow-sm inline-block">
                        <div id="qrcode"></div>
                    </div>
                </div>



                <button id="fullscreen-btn" onclick="requestFullscreenQr()" class="w-full bg-brand-600 text-white rounded-xl py-2.5 font-bold mb-2 transition hover:bg-brand-700">
                    Layar Penuh
                </button>

                <button id="close-btn" onclick="closeQrModal()" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3 rounded-xl transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <style>
        #qr-modal-content:fullscreen {
            width: 100vw !important;
            height: 100vh !important;
            max-width: none !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            border-radius: 0 !important;
            background-color: #ffffff !important;
            padding: 2rem !important;
        }
    </style>

    <!-- Library QR Code Generator -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        let qrcodeObj = null;

        let pollIntervalId = null;
        let currentJadwalId = null;
        let currentMapelName = null;
        let currentQrType = null;
        let countdownSeconds = 30;

        function showQrModal(jadwalId, mapelName, type) {
            const modal = document.getElementById('qr-modal');
            const modalContent = document.getElementById('qr-modal-content');

            // Simpan variabel luar
            currentJadwalId = jadwalId;
            currentMapelName = mapelName;
            currentQrType = type;

            // Set judul
            document.getElementById('qr-mapel-name').innerText = mapelName;

            // Render QR Awal
            renderQrCode();

            // Mulai Timer
            startQrTimers();

            // Tampilkan Modal dengan animasi
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // Sedikit delay untuk trigger animasi transisi Tailwind
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function renderQrCode() {
            const qrElement = document.getElementById('qrcode');
            qrElement.innerHTML = '';

            const isFullscreen = document.fullscreenElement === document.getElementById('qr-modal-content');
            const qrSize = isFullscreen ? 320 : 220;

            const payload = {
                type: 'absen_jadwal',
                jadwal_id: currentJadwalId,
                timestamp: Date.now()
            };

            new QRCode(qrElement, {
                text: JSON.stringify(payload),
                width: qrSize,
                height: qrSize,
                colorDark : "#0f172a",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        }

        function startQrTimers() {
            stopQrTimers();


            // Polling status absensi ke server
            pollIntervalId = setInterval(() => {
                fetch(`/siswa/jadwal/${currentJadwalId}/status`)
                    .then(res => res.json())
                    .then(data => {
                        if (currentQrType === 'masuk' && data.absen_masuk) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Berhasil!', 'Guru telah melakukan scan QR MASUK.', 'success').then(() => location.reload());
                            } else {
                                alert('Berhasil! Guru telah melakukan scan QR MASUK.');
                                location.reload();
                            }
                        } else if (currentQrType === 'keluar' && data.absen_keluar) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Berhasil!', 'Guru telah melakukan scan QR KELUAR.', 'success').then(() => location.reload());
                            } else {
                                alert('Berhasil! Guru telah melakukan scan QR KELUAR.');
                                location.reload();
                            }
                        }
                    })
                    .catch(err => console.error(err));
            }, 3000);
        }

        // ... Sisa fungsi JS di bawah ...
        function stopQrTimers() {

            if (pollIntervalId) {
                clearInterval(pollIntervalId);
                pollIntervalId = null;
            }
        }


        function requestFullscreenQr() {
            const modalContent = document.getElementById('qr-modal-content');
            if (modalContent.requestFullscreen) {
                modalContent.requestFullscreen();
            }
        }

        function closeQrModal() {
            const modal = document.getElementById('qr-modal');
            const modalContent = document.getElementById('qr-modal-content');

            // Matikan timers saat modal ditutup
            stopQrTimers();

            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300); // Waktu yang sama dengan durasi transisi
        }

        // Listener untuk reset ukuran saat keluar fullscreen
        document.addEventListener('fullscreenchange', () => {
            const modalContent = document.getElementById('qr-modal-content');
            const isFullscreen = document.fullscreenElement === modalContent;
            const fullscreenBtn = document.getElementById('fullscreen-btn');
            const closeBtn = document.getElementById('close-btn');

            if (isFullscreen) {
                if (fullscreenBtn) fullscreenBtn.style.display = 'none';
                if (closeBtn) closeBtn.style.display = 'none';
            } else {
                if (fullscreenBtn) fullscreenBtn.style.display = 'block';
                if (closeBtn) closeBtn.style.display = 'block';
            }

            // Regenerate QR dengan ukuran baru
            renderQrCode();
        });

        // --- Countdown card ke jadwal berikutnya ---
        const targetTime = @json($nextJamMulai);
        const el = document.getElementById('next-class-countdown');
        if (targetTime && el) {
            function tickCountdown() {
                const now = new Date();
                const parts = targetTime.split(':');
                const target = new Date();
                target.setHours(parseInt(parts[0], 10), parseInt(parts[1], 10), parseInt(parts[2] || 0, 10), 0);
                const diff = Math.floor((target - now) / 1000);
                if (diff <= 0) {
                    el.textContent = '00:00:00';
                    const parentCard = el.closest('.bg-brand-50');
                    if (parentCard) {
                        const subEl = parentCard.querySelector('p.text-sm');
                        if (subEl) subEl.textContent = 'Kelas dimulai sekarang!';
                    }
                    return;
                }
                const h = Math.floor(diff / 3600);
                const m = Math.floor((diff % 3600) / 60);
                const s = diff % 60;
                el.textContent =
                    String(h).padStart(2, '0') + ':' +
                    String(m).padStart(2, '0') + ':' +
                    String(s).padStart(2, '0');
            }
            tickCountdown();
            setInterval(tickCountdown, 1000);
        }

        // --- Unlock tombol Generate QR secara real-time ---
        const lockedContainers = document.querySelectorAll('[data-unlock-time]');
        if (lockedContainers.length > 0) {
            function padTwo(n) { return String(n).padStart(2, '0'); }

            function checkUnlock() {
                const now = new Date();
                lockedContainers.forEach(container => {
                    const unlockTime = container.getAttribute('data-unlock-time');
                    const parts = unlockTime.split(':');
                    const target = new Date();
                    target.setHours(parseInt(parts[0], 10), parseInt(parts[1], 10), 0, 0);

                    const diff = Math.floor((target - now) / 1000);

                    const lockedBtn  = container.querySelector('.qr-locked-btn');
                    const unlockedBtn = container.querySelector('.qr-unlocked-btn');
                    const countdownEl = container.querySelector('.qr-unlock-countdown');

                    if (diff <= 0) {
                        if (lockedBtn)   { lockedBtn.style.display = 'none'; }
                        if (unlockedBtn) { unlockedBtn.style.display = 'block'; }
                    } else {
                        if (countdownEl) {
                            const mm = Math.floor(diff / 60);
                            const ss = diff % 60;
                            countdownEl.textContent = padTwo(mm) + ':' + padTwo(ss);
                        }
                    }
                });
            }

            checkUnlock();
            setInterval(checkUnlock, 1000);
        }
    </script>
@endif

@endsection
