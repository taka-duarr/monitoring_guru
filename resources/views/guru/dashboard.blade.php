@extends('layouts.guru')
@section('title', 'Jadwal Mengajar')
@section('content_class', 'no-padding')

@section('content')

<style>
    .dash-wrap {
        min-height: 100vh;
        background: #f8fafc;
    }
    .dash-inner { padding: 20px; }

    /* Glass helper — very subtle */
    .card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .card-sm {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    /* Stat mini cards */
    .stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 10px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .stat-num { font-size:24px; font-weight: 800; color: #0f172a; line-height: 1; margin: 0; }
    .stat-lbl { font-size:12px; color: #94a3b8; margin: 4px 0 0; text-transform: uppercase; letter-spacing: .5px; }

    /* Badge */
    .badge-time {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size:12px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 99px;
        font-family: monospace;
        letter-spacing: .3px;
        white-space: nowrap;
    }
    .badge-status-done {
        background: #f1f5f9;
        color: #64748b;
        font-size:12px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 99px;
    }
    .badge-status-pending {
        background: #eff6ff;
        color: #3b82f6;
        font-size:12px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 99px;
    }

    /* Schedule cards */
    .sched-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        transition: box-shadow .18s, border-color .18s, transform .15s;
        display: flex; flex-direction: column; gap: 8px;
    }
    .sched-card:hover {
        border-color: #94a3b8;
        box-shadow: 0 4px 14px rgba(0,0,0,0.07);
        transform: translateY(-1px);
    }
    .sched-card.done { background: #f8fafc; }

    /* All-schedule small cards */
    .mini-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
        text-decoration: none;
        display: flex; flex-direction: column; gap: 5px;
        transition: border-color .15s, box-shadow .15s, transform .12s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .mini-card:hover {
        border-color: #94a3b8;
        box-shadow: 0 4px 14px rgba(0,0,0,0.07);
        transform: translateY(-1px);
    }

    /* Ongoing live card */
    .live-card {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: 0 2px 8px rgba(234,179,8,0.08);
        position: relative; overflow: hidden;
    }
    .live-bar {
        position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, #f59e0b, #fbbf24, #f59e0b);
        background-size: 200% 100%;
        animation: shimmer-bar 2s linear infinite;
    }

    /* Scan button */
    .btn-scan {
        background: #0f172a;
        color: #ffffff;
        border: none;
        border-radius: 11px;
        padding: 10px 16px;
        font-size:15px;
        font-weight: 700;
        text-decoration: none;
        display: flex; align-items: center; justify-content: center; gap: 7px;
        cursor: pointer;
        transition: background .15s;
    }
    .btn-scan:hover { background: #1e293b; }
    .btn-scan-ghost {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
        padding: 10px 16px;
        font-size:15px;
        font-weight: 700;
        text-decoration: none;
        display: flex; align-items: center; justify-content: center; gap: 7px;
        cursor: pointer;
        transition: background .15s;
    }
    .btn-scan-ghost:hover { background: #f1f5f9; }
    .btn-locked {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #94a3b8;
        border-radius: 11px;
        padding: 10px 16px;
        font-size:15px;
        font-weight: 600;
        display: flex; align-items: center; justify-content: center; gap: 7px;
        cursor: not-allowed;
    }

    /* Filter select */
    .filter-select {
        width: 100%;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
        padding: 9px 13px;
        font-size:15px;
        color: #0f172a;
        outline: none;
        appearance: none;
        cursor: pointer;
    }

    /* Section divider labels */
    .section-label {
        font-size:12px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        margin: 0;
    }

    /* Day pill */
    .day-pill-today {
        background: #0f172a;
        color: #ffffff;
        font-size:12px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 99px;
    }
    .day-pill {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
        font-size:12px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 99px;
    }

    @keyframes shimmer-bar {
        0%   { background-position: -200% 0; }
        100% { background-position:  200% 0; }
    }
    @keyframes ping-dot {
        0%,100% { opacity:1; }
        50%      { opacity:.4; }
    }
</style>

<div class="dash-wrap">
<div class="dash-inner">

    @php
        $totalJadwal    = count($allJadwals);
        $selesaiJadwal  = $allJadwals->filter(fn($j) => $j->absen_masuk !== null && $j->absen_keluar !== null)->count();
        $belumJadwal    = $allJadwals->filter(fn($j) => $j->absen_masuk === null)->count();
        $nextJadwal     = $allJadwals->first(fn($j) => $j->absen_masuk === null);
        $nextJamMulai   = $nextJadwal ? $nextJadwal->jam_mulai : null;
        $ongoingClasses = $allJadwals->filter(fn($j) => $j->absen_masuk !== null && $j->absen_keluar === null);
        $otherClasses   = $jadwals->filter(fn($j) => !($j->absen_masuk !== null && $j->absen_keluar === null));
    @endphp

    {{-- ─── HEADER ─── --}}
    <div style="margin-bottom:22px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <div style="width:44px;height:44px;border-radius:12px;background:#0f172a;
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="ti ti-chalkboard-teacher" style="color:#ffffff;font-size:22px;"></i>
            </div>
            <div>
                <h2 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;letter-spacing:-.3px;">
                    Halo, {{ Auth::user()->name }}
                </h2>
                <p style="color:#94a3b8;font-size:14px;margin:2px 0 0;">
                    {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                </p>
            </div>
        </div>

        {{-- Tips dismissible --}}
        <div x-data="{ show: true }" x-show="show" style="display:none;"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0">
            <div class="card-sm" style="padding:12px 14px;display:flex;align-items:flex-start;gap:10px;
                                         position:relative;margin-bottom:16px;">
                <i class="ti ti-info-circle" style="color:#64748b;font-size:20px;flex-shrink:0;margin-top:1px;"></i>
                <div style="flex:1;">
                    <p style="font-weight:700;font-size:14px;color:#334155;margin:0 0 2px;">Tips Absensi</p>
                    <p style="font-size:14px;color:#64748b;margin:0;">Minta Ketua Kelas tampilkan QR, lalu tap <strong style="color:#0f172a;">Scan QR MASUK</strong> untuk mencatat kehadiran.</p>
                </div>
                <button @click="show=false" style="background:none;border:none;cursor:pointer;color:#cbd5e1;font-size:15px;padding:1px;position:absolute;top:10px;right:12px;">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        </div>

        {{-- Stats row --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:12px;">
            <div class="stat-card">
                <p class="stat-num">{{ $totalJadwal }}</p>
                <p class="stat-lbl">Jadwal</p>
            </div>
            <div class="stat-card">
                <p class="stat-num" style="color:#16a34a;">{{ $selesaiJadwal }}</p>
                <p class="stat-lbl">Selesai</p>
            </div>
            <div class="stat-card">
                <p class="stat-num" style="color:#64748b;">{{ $belumJadwal }}</p>
                <p class="stat-lbl">Belum</p>
            </div>
        </div>

        {{-- Countdown --}}
        <div class="card" style="padding:15px 16px;">
            @if($nextJamMulai)
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                    <div>
                        <p style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;
                                   letter-spacing:1px;margin:0 0 4px;display:flex;align-items:center;gap:4px;">
                            <i class="ti ti-clock-hour-4" style="font-size:15px;"></i> Kelas Berikutnya
                        </p>
                        <p style="font-size:15px;color:#334155;margin:0;">
                            Pukul <strong style="color:#0f172a;">{{ substr($nextJamMulai, 0, 5) }}</strong>
                        </p>
                    </div>
                    <div style="text-align:right;">
                        <p id="next-countdown" style="font-size:26px;font-weight:900;color:#0f172a;
                                                       font-family:monospace;letter-spacing:-1px;line-height:1;margin:0;">--:--:--</p>
                        <p style="font-size:11px;color:#cbd5e1;margin:2px 0 0;text-transform:uppercase;letter-spacing:.5px;">Hitung Mundur</p>
                    </div>
                </div>
            @else
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:#f1f5f9;
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ti ti-circle-check" style="color:#16a34a;font-size:22px;"></i>
                    </div>
                    <div>
                        <p style="font-weight:700;color:#0f172a;font-size:16px;margin:0;">Semua Selesai!</p>
                        <p style="color:#94a3b8;font-size:14px;margin:0;">Tidak ada jadwal lagi hari ini</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- 1. KELAS BERLANGSUNG                       --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if($ongoingClasses->isNotEmpty())
    <div style="margin-bottom:22px;">
        <div style="display:flex;align-items:center;gap:7px;margin-bottom:10px;">
            <div style="width:6px;height:6px;border-radius:50%;background:#f59e0b;animation:ping-dot 1.4s ease-in-out infinite;"></div>
            <p class="section-label" style="color:#92400e;">Sedang Berlangsung</p>
        </div>
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach($ongoingClasses as $jadwal)
            <div class="live-card">
                <div class="live-bar"></div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;flex-wrap:wrap;">
                            <span class="badge-time">{{ substr($jadwal->jam_mulai,0,5) }} – {{ substr($jadwal->jam_selesai,0,5) }}</span>
                            <span style="background:#fef3c7;border:1px solid #fde68a;color:#92400e;
                                         font-size:11px;font-weight:700;padding:2px 8px;border-radius:99px;
                                         display:inline-flex;align-items:center;gap:3px;">
                                <span style="width:5px;height:5px;border-radius:50%;background:#f59e0b;display:inline-block;
                                             animation:ping-dot 1s ease-in-out infinite;"></span> LIVE
                            </span>
                        </div>
                        <a href="{{ route('guru.riwayat_jadwal', $jadwal->id) }}" style="text-decoration:none;">
                            <p style="font-size:17px;font-weight:700;color:#0f172a;margin:0 0 2px;
                                       white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $jadwal->mapel->name ?? 'Mata Pelajaran' }}
                            </p>
                        </a>
                        <p style="font-size:13px;color:#78716c;margin:0;">
                            Kelas {{ ($jadwal->kelas->grade ?? '') . ' ' . ($jadwal->kelas->name ?? '-') }}
                            &nbsp;·&nbsp; {{ $jadwal->ruangan->name ?? '-' }}
                            &nbsp;·&nbsp; Masuk: <strong style="color:#0f172a;">{{ substr($jadwal->absen_masuk->jam_masuk,0,5) }}</strong>
                        </p>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:6px;flex-shrink:0;">
                        <a href="{{ route('guru.scan') }}" class="btn-scan" style="font-size:13px;padding:7px 12px;">
                            <i class="ti ti-qrcode" style="font-size:16px;"></i> Scan Keluar
                        </a>
                        <a href="{{ route('guru.absen_murid', $jadwal->absen_masuk->id) }}" class="btn-scan-ghost" style="font-size:13px;padding:7px 12px;">
                            <i class="ti ti-users" style="font-size:16px;"></i> Absen Murid
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════ --}}
    {{-- 2. JADWAL HARI INI                         --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div style="margin-bottom:8px;">
        <div style="display:flex;align-items:center;gap:7px;margin-bottom:10px;">
            <div style="width:5px;height:5px;border-radius:50%;background:#cbd5e1;"></div>
            <p class="section-label">Jadwal — {{ $hariIni }}</p>
        </div>

        <div style="display:flex;flex-direction:column;gap:8px;">
            @forelse($otherClasses as $jadwal)
            @php
                $hasMasuk  = $jadwal->absen_masuk !== null;
                $hasKeluar = $jadwal->absen_keluar !== null;
                $jamMulaiCarbon = \Carbon\Carbon::createFromFormat('H:i', substr($jadwal->jam_mulai, 0, 5));
                $isTimeToScan   = \Carbon\Carbon::now()->gte($jamMulaiCarbon);
                $isDone = $hasMasuk && $hasKeluar;
                $canScan = !$isDone;
            @endphp

            <div class="sched-card {{ $isDone ? 'done' : '' }}">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:5px;flex-wrap:wrap;">
                            <span class="badge-time">{{ substr($jadwal->jam_mulai,0,5) }} – {{ substr($jadwal->jam_selesai,0,5) }}</span>
                            @if($isDone)
                                <span class="badge-status-done">Selesai</span>
                            @else
                                <span class="badge-status-pending">Belum Absen</span>
                            @endif
                        </div>
                        <a href="{{ route('guru.riwayat_jadwal', $jadwal->id) }}" style="text-decoration:none;">
                            <h4 style="font-size:17px;font-weight:700;color:#0f172a;margin:0 0 3px;
                                       white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $jadwal->mapel->name ?? 'Mata Pelajaran' }}
                            </h4>
                        </a>
                        <p style="font-size:14px;color:#64748b;margin:0;">
                            Kelas {{ ($jadwal->kelas->grade ?? '') . ' ' . ($jadwal->kelas->name ?? '-') }}
                            &nbsp;·&nbsp; {{ $jadwal->ruangan->name ?? '-' }}
                        </p>
                    </div>
                </div>

                @if($hasMasuk)
                <div style="display:flex;align-items:center;gap:10px;padding:7px 11px;
                            background:#f8fafc;border-radius:9px;font-size:14px;border:1px solid #f1f5f9;">
                    <i class="ti ti-login" style="color:#64748b;font-size:15px;"></i>
                    <span style="color:#334155;font-weight:600;">{{ substr($jadwal->absen_masuk->jam_masuk,0,5) }}</span>
                    @if($hasKeluar)
                        <i class="ti ti-arrow-right" style="color:#cbd5e1;font-size:14px;"></i>
                        <i class="ti ti-logout" style="color:#64748b;font-size:15px;"></i>
                        <span style="color:#334155;font-weight:600;">{{ substr($jadwal->absen_keluar->jam_keluar,0,5) }}</span>
                    @else
                        <span style="color:#cbd5e1;font-size:13px;margin-left:2px;">Belum keluar</span>
                    @endif
                </div>
                @endif

                @if($canScan)
                <div>
                    @if($isTimeToScan)
                    <a href="{{ route('guru.scan') }}" class="btn-scan">
                        <i class="ti ti-qrcode" style="font-size:17px;"></i> Scan QR MASUK
                    </a>
                    @else
                    <div class="relative" data-unlock-time="{{ $jadwal->jam_mulai }}">
                        <div class="btn-locked scan-locked-btn">
                            <i class="ti ti-lock" style="font-size:16px;"></i>
                            <span>Terbuka pukul <strong style="color:#475569;">{{ substr($jadwal->jam_mulai,0,5) }}</strong>
                                (<span class="scan-unlock-countdown" style="font-family:monospace;font-size:13px;">--:--</span>)
                            </span>
                        </div>
                        <a href="{{ route('guru.scan') }}" class="btn-scan scan-unlocked-btn" style="display:none;">
                            <i class="ti ti-qrcode" style="font-size:17px;"></i> Scan QR MASUK
                        </a>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @empty
                @if($ongoingClasses->isEmpty())
                <div class="card" style="padding:36px 20px;text-align:center;">
                    <div style="width:52px;height:52px;border-radius:16px;background:#f1f5f9;
                                display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <i class="ti ti-calendar-off" style="color:#94a3b8;font-size:26px;"></i>
                    </div>
                    <p style="font-weight:700;color:#0f172a;font-size:16px;margin:0 0 4px;">Tidak ada jadwal hari {{ $hariIni }}</p>
                    <p style="color:#94a3b8;font-size:14px;margin:0;">Nikmati harimu!</p>
                </div>
                @endif
            @endforelse
        </div>
        <div class="mt-5 flex justify-center">{{ $jadwals->links() }}</div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- 3. SEMUA JADWAL + FILTER                   --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div style="margin-top:28px;">

        {{-- Filter --}}
        <form method="GET" action="{{ route('guru.dashboard') }}"
              class="card" style="margin-bottom:18px;overflow:hidden;">
            <div style="display:flex;align-items:center;gap:7px;padding:10px 16px;
                        background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                <i class="ti ti-filter" style="color:#64748b;font-size:15px;"></i>
                <span style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:1px;">Filter Data</span>
            </div>
            <div style="padding:14px 16px;display:flex;flex-direction:column;gap:10px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#94a3b8;
                                   text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" class="filter-select">
                        <option value="">— Semua Tahun Ajaran —</option>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}" {{ $selectedTahunAjaranId == $ta->id ? 'selected' : '' }}>
                                {{ $ta->name }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex;gap:8px;">
                    <div style="display:flex;gap:8px;align-items:center;flex:1;">
                        <button type="submit" class="btn-scan" style="flex:1;">
                            <i class="ti ti-filter" style="font-size:15px;"></i> Tampilkan
                        </button>
                        @if($selectedTahunAjaranId)
                        <a href="{{ route('guru.dashboard') }}"
                           style="width:40px;height:40px;flex-shrink:0;background:#f8fafc;border:1px solid #e2e8f0;
                                  border-radius:11px;display:flex;align-items:center;justify-content:center;
                                  color:#64748b;text-decoration:none;">
                            <i class="ti ti-x" style="font-size:17px;"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        {{-- Section label --}}
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
            <p class="section-label" style="white-space:nowrap;">Semua Jadwal Mengajar</p>
            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
            @if($selectedTahunAjaran)
                <span style="background:#f1f5f9;border:1px solid #e2e8f0;color:#475569;
                             font-size:12px;font-weight:700;padding:3px 10px;border-radius:99px;white-space:nowrap;">
                    {{ $selectedTahunAjaran->name }}
                </span>
            @endif
            <span style="color:#cbd5e1;font-size:12px;white-space:nowrap;">{{ $jadwalSemua->flatten()->count() }} jadwal</span>
        </div>

        @if($jadwalSemua->isEmpty())
        <div class="card" style="padding:36px;text-align:center;">
            <i class="ti ti-calendar-off" style="font-size:34px;color:#cbd5e1;display:block;margin-bottom:10px;"></i>
            <p style="color:#94a3b8;font-size:16px;margin:0;">Tidak ada jadwal untuk tahun ajaran ini.</p>
        </div>
        @endif

        <div style="display:flex;flex-direction:column;gap:20px;">
            @php $urutan = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu']; @endphp
            @foreach($urutan as $namaHari)
                @if($jadwalSemua->has($namaHari))
                    @php $kelasHari = $jadwalSemua[$namaHari]; @endphp
                    <div>
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:9px;">
                            <span class="{{ $namaHari === $hariIni ? 'day-pill-today' : 'day-pill' }}">
                                {{ $namaHari }}@if($namaHari === $hariIni)<span style="opacity:.7;margin-left:3px;">· Hari Ini</span>@endif
                            </span>
                            <div style="flex:1;height:1px;background:#f1f5f9;"></div>
                            <span style="font-size:12px;color:#cbd5e1;">{{ count($kelasHari) }} kelas</span>
                        </div>

                        <div style="display:flex;flex-direction:column;gap:8px;">
                            @foreach($kelasHari as $j)
                            <a href="{{ route('guru.riwayat_jadwal', $j->id) }}" class="mini-card">
                                <div style="display:flex;align-items:center;justify-content:space-between;">
                                    <span class="badge-time">{{ substr($j->jam_mulai,0,5) }} – {{ substr($j->jam_selesai,0,5) }}</span>
                                    <i class="ti ti-chevron-right" style="color:#cbd5e1;font-size:14px;"></i>
                                </div>
                                <h4 style="font-size:15px;font-weight:700;color:#0f172a;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $j->mapel->name ?? 'Mata Pelajaran' }}
                                </h4>
                                <p style="font-size:13px;color:#94a3b8;margin:0;display:flex;align-items:center;gap:3px;flex-wrap:wrap;">
                                    <i class="ti ti-school" style="font-size:13px;"></i>
                                    {{ ($j->kelas->grade ?? '') . ' ' . ($j->kelas->name ?? '-') }}
                                    <span style="color:#e2e8f0;">·</span>
                                    <i class="ti ti-door" style="font-size:13px;"></i>
                                    {{ $j->ruangan->name ?? '-' }}
                                </p>
                                <div style="padding-top:5px;border-top:1px solid #f1f5f9;
                                            font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:3px;">
                                    <i class="ti ti-history" style="font-size:13px;"></i> Lihat Riwayat
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

</div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    var targetTime = @json($nextJamMulai);
    var el = document.getElementById('next-countdown');
    if (targetTime && el) {
        function tick() {
            var now = new Date(), p = targetTime.split(':');
            var t = new Date(); t.setHours(+p[0], +p[1], +(p[2]||0), 0);
            var d = Math.floor((t - now) / 1000);
            if (d <= 0) { el.textContent = '00:00:00'; return; }
            el.textContent = [Math.floor(d/3600), Math.floor((d%3600)/60), d%60]
                .map(n => String(n).padStart(2,'0')).join(':');
        }
        tick(); setInterval(tick, 1000);
    }

    var locked = document.querySelectorAll('[data-unlock-time]');
    if (!locked.length) return;
    function pad(n){ return String(n).padStart(2,'0'); }
    function check() {
        var now = new Date();
        locked.forEach(function(c) {
            var p = c.getAttribute('data-unlock-time').split(':');
            var t = new Date(); t.setHours(+p[0], +p[1], 0, 0);
            var d = Math.floor((t - now) / 1000);
            var lb = c.querySelector('.scan-locked-btn');
            var ub = c.querySelector('.scan-unlocked-btn');
            var ce = c.querySelector('.scan-unlock-countdown');
            if (d <= 0) {
                if (lb) lb.style.display = 'none';
                if (ub) ub.style.display = 'flex';
            } else {
                if (ce) ce.textContent = pad(Math.floor(d/60)) + ':' + pad(d%60);
            }
        });
    }
    check(); setInterval(check, 1000);
})();
</script>
@endpush
