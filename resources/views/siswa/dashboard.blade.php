@extends('layouts.siswa')
@section('title', 'Ruang Kelas')
@section('content_class', 'p-5')

@section('content')

{{-- ══════════════════════════════════════════════════════ --}}
{{-- KELAS TIDAK AKTIF / BELUM DITUGASKAN                  --}}
{{-- ══════════════════════════════════════════════════════ --}}
@if(!$kelas)
<div style="min-height:60vh;display:flex;align-items:center;justify-content:center;">
    <div style="text-align:center;max-width:320px;">
        <div style="width:56px;height:56px;border-radius:16px;background:#f1f5f9;
                    display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <i class="ti ti-school-off" style="color:#94a3b8;font-size:26px;"></i>
        </div>
        <p style="font-weight:700;color:#0f172a;font-size:18px;margin:0 0 6px;">Belum Ditugaskan</p>
        <p style="color:#94a3b8;font-size:15px;margin:0;">Kamu belum ditugaskan ke kelas manapun.</p>
    </div>
</div>

@elseif($kelas && !$kelas->is_active)
<div style="min-height:60vh;display:flex;align-items:center;justify-content:center;">
    <div style="text-align:center;max-width:320px;">
        <div style="width:56px;height:56px;border-radius:16px;background:#fef3c7;
                    display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <i class="ti ti-alert-triangle" style="color:#f59e0b;font-size:26px;"></i>
        </div>
        <p style="font-weight:700;color:#0f172a;font-size:18px;margin:0 0 6px;">Kelas Nonaktif</p>
        <p style="color:#94a3b8;font-size:15px;margin:0;">
            Kelas <strong style="color:#475569;">{{ $kelas->name }}</strong> sudah tidak aktif. Data absen tidak dapat ditampilkan.
        </p>
    </div>
</div>

@elseif($kelas && $kelas->is_active)

<style>
    .s-card     { background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.03); }
    .s-card-sm  { background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.03); }
    .s-stat     { background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px 10px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,.03); }
    .s-badge-t  { background:#f1f5f9;border:1px solid #e2e8f0;color:#475569;font-size:12px;font-weight:700;padding:3px 10px;border-radius:99px;font-family:monospace;white-space:nowrap; }
    .s-sched    { background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px 16px;box-shadow:0 1px 3px rgba(0,0,0,.03);display:flex;flex-direction:column;gap:8px;transition:border-color .15s,box-shadow .15s; }
    .s-sched.done { background:#f8fafc; }
    .s-mini     { background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:12px 14px;display:flex;flex-direction:column;gap:5px;box-shadow:0 1px 3px rgba(0,0,0,.03);transition:border-color .15s,box-shadow .15s; }
    .s-btn      { background:#0f172a;color:#fff;border:none;border-radius:11px;padding:10px 16px;font-size:15px;font-weight:700;display:flex;align-items:center;justify-content:center;gap:7px;cursor:pointer;transition:background .15s;text-decoration:none;width:100%; }
    .s-btn:hover { background:#1e293b; }
    .s-btn-ghost { background:#f8fafc;color:#475569;border:1px solid #e2e8f0;border-radius:11px;padding:10px 16px;font-size:15px;font-weight:700;display:flex;align-items:center;justify-content:center;gap:7px;cursor:pointer;text-decoration:none;width:100%;transition:background .15s; }
    .s-btn-ghost:hover { background:#f1f5f9; }
    .s-btn-locked { background:#f8fafc;border:1px solid #e2e8f0;color:#94a3b8;border-radius:11px;padding:10px 16px;font-size:15px;font-weight:600;display:flex;align-items:center;justify-content:center;gap:7px;cursor:not-allowed;width:100%; }
    .s-lbl      { font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1.2px;margin:0; }
    .day-t      { background:#0f172a;color:#fff;font-size:12px;font-weight:700;padding:4px 12px;border-radius:99px; }
    .day-n      { background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;font-size:12px;font-weight:700;padding:4px 12px;border-radius:99px; }
    .live-card  { background:#fffbeb;border:1px solid #fde68a;border-radius:14px;padding:14px 16px;box-shadow:0 2px 8px rgba(234,179,8,.08);position:relative;overflow:hidden; }
    .live-bar   { position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,#f59e0b,#fbbf24,#f59e0b);background-size:200% 100%;animation:shimmer-bar 2s linear infinite; }
    @keyframes shimmer-bar { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
    @keyframes ping-dot    { 0%,100%{opacity:1} 50%{opacity:.4} }
</style>

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
            <i class="ti ti-users-group" style="color:#fff;font-size:22px;"></i>
        </div>
        <div>
            <h2 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;letter-spacing:-.3px;">
                Kelas {{ $kelas->grade ? $kelas->grade . ' ' : '' }}{{ $kelas->name }}
            </h2>
            <p style="color:#94a3b8;font-size:14px;margin:2px 0 0;">
                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </p>
        </div>
    </div>

    {{-- Tips dismissible --}}
    <div x-data="{ show: true }" x-show="show" style="display:none;"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0">
        <div class="s-card-sm" style="padding:12px 14px;display:flex;align-items:flex-start;gap:10px;position:relative;margin-bottom:16px;">
            <i class="ti ti-info-circle" style="color:#64748b;font-size:20px;flex-shrink:0;margin-top:1px;"></i>
            <div style="flex:1;">
                <p style="font-weight:700;font-size:14px;color:#334155;margin:0 0 2px;">Panduan Presensi</p>
                <p style="font-size:14px;color:#64748b;margin:0;">Ketuk <strong style="color:#0f172a;">Generate QR MASUK / KELUAR</strong> lalu tunjukkan QR ke Guru untuk mencatat kehadiran.</p>
            </div>
            <button @click="show=false" style="background:none;border:none;cursor:pointer;color:#cbd5e1;font-size:15px;position:absolute;top:10px;right:12px;">
                <i class="ti ti-x"></i>
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:12px;">
        <div class="s-stat">
            <p style="font-size:24px;font-weight:800;color:#0f172a;line-height:1;margin:0;">{{ $totalJadwal }}</p>
            <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;text-transform:uppercase;letter-spacing:.5px;">Jadwal</p>
        </div>
        <div class="s-stat">
            <p style="font-size:24px;font-weight:800;color:#16a34a;line-height:1;margin:0;">{{ $selesaiJadwal }}</p>
            <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;text-transform:uppercase;letter-spacing:.5px;">Selesai</p>
        </div>
        <div class="s-stat">
            <p style="font-size:24px;font-weight:800;color:#64748b;line-height:1;margin:0;">{{ $belumJadwal }}</p>
            <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;text-transform:uppercase;letter-spacing:.5px;">Belum</p>
        </div>
    </div>

    {{-- Countdown --}}
    <div class="s-card" style="padding:15px 16px;">
        @if($nextJamMulai)
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <div>
                    <p style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;
                               margin:0 0 4px;display:flex;align-items:center;gap:4px;">
                        <i class="ti ti-clock-hour-4" style="font-size:15px;"></i> Kelas Berikutnya
                    </p>
                    <p style="font-size:15px;color:#334155;margin:0;">Pukul <strong style="color:#0f172a;">{{ substr($nextJamMulai, 0, 5) }}</strong></p>
                </div>
                <div style="text-align:right;">
                    <p id="next-class-countdown" style="font-size:26px;font-weight:900;color:#0f172a;font-family:monospace;letter-spacing:-1px;line-height:1;margin:0;">--:--:--</p>
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

{{-- ══════════════════════════════════════════════════════ --}}
{{-- 1. KELAS SEDANG BERLANGSUNG                           --}}
{{-- ══════════════════════════════════════════════════════ --}}
@if($ongoingClasses->isNotEmpty())
<div style="margin-bottom:22px;">
    <div style="display:flex;align-items:center;gap:7px;margin-bottom:10px;">
        <div style="width:6px;height:6px;border-radius:50%;background:#f59e0b;animation:ping-dot 1.4s ease-in-out infinite;"></div>
        <p class="s-lbl" style="color:#92400e;">Sedang Berlangsung</p>
    </div>
    <div style="display:flex;flex-direction:column;gap:8px;">
        @foreach($ongoingClasses as $jadwal)
        <div class="live-card">
            <div class="live-bar"></div>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;flex-wrap:wrap;">
                        <span class="s-badge-t">{{ substr($jadwal->jam_mulai,0,5) }} – {{ substr($jadwal->jam_selesai,0,5) }}</span>
                        <span style="background:#fef3c7;border:1px solid #fde68a;color:#92400e;
                                     font-size:11px;font-weight:700;padding:2px 8px;border-radius:99px;
                                     display:inline-flex;align-items:center;gap:3px;">
                            <span style="width:5px;height:5px;border-radius:50%;background:#f59e0b;display:inline-block;animation:ping-dot 1s ease-in-out infinite;"></span> LIVE
                        </span>
                    </div>
                    <p style="font-size:17px;font-weight:700;color:#0f172a;margin:0 0 2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $jadwal->mapel->name ?? 'Mata Pelajaran' }}
                    </p>
                    <p style="font-size:13px;color:#78716c;margin:0;display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                        <span>Guru: <strong style="color:#0f172a;">{{ $jadwal->guru->name ?? '-' }}</strong></span>
                        @if(isset($jadwal->guru->no_telp) && $jadwal->guru->no_telp)
                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $jadwal->guru->no_telp)) }}" target="_blank" style="color:#64748b;font-size:12px;text-decoration:none;display:flex;align-items:center;gap:3px;background:#f8fafc;padding:2px 6px;border-radius:6px;border:1px solid #e2e8f0;"><i class="ti ti-brand-whatsapp" style="color:#16a34a;font-size:13px;"></i> {{ $jadwal->guru->no_telp }}</a>
                        @endif
                        <span>&nbsp;·&nbsp; {{ $jadwal->ruangan->name ?? '-' }}</span>
                    </p>
                </div>
                <div style="flex-shrink:0;">
                    <button onclick="showQrModal('{{ $jadwal->id }}', '{{ addslashes($jadwal->mapel->name ?? 'Mapel') }}', 'keluar')"
                            style="background:linear-gradient(135deg,#f59e0b,#ea580c);color:#fff;border:none;border-radius:11px;
                                   padding:9px 14px;font-size:14px;font-weight:700;cursor:pointer;
                                   display:flex;align-items:center;gap:5px;white-space:nowrap;
                                   box-shadow:0 3px 10px rgba(245,158,11,.25);">
                        <i class="ti ti-qrcode" style="font-size:16px;"></i> QR Keluar
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════ --}}
{{-- 2. JADWAL HARI INI                                    --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div style="margin-bottom:8px;">
    <div style="display:flex;align-items:center;gap:7px;margin-bottom:10px;">
        <div style="width:5px;height:5px;border-radius:50%;background:#cbd5e1;"></div>
        <p class="s-lbl">Jadwal — {{ $hariIni }}</p>
    </div>

    <div style="display:flex;flex-direction:column;gap:8px;">
        @forelse($otherClasses as $jadwal)
        @php
            $hasMasuk  = $jadwal->absen_masuk !== null;
            $hasKeluar = $jadwal->absen_keluar !== null;
            $jamMulaiCarbon = \Carbon\Carbon::createFromFormat('H:i', substr($jadwal->jam_mulai, 0, 5));
            $isTimeToScan   = \Carbon\Carbon::now()->gte($jamMulaiCarbon);
            $isDone = $hasMasuk && $hasKeluar;
            $canGenerate = !$isDone;
        @endphp

        <div class="s-sched {{ $isDone ? 'done' : '' }}">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:5px;flex-wrap:wrap;">
                        <span class="s-badge-t">{{ substr($jadwal->jam_mulai,0,5) }} – {{ substr($jadwal->jam_selesai,0,5) }}</span>
                        @if($isDone)
                            <span style="background:#f1f5f9;color:#64748b;font-size:12px;font-weight:700;padding:2px 9px;border-radius:99px;">Selesai</span>
                        @else
                            <span style="background:#eff6ff;color:#3b82f6;font-size:12px;font-weight:700;padding:2px 9px;border-radius:99px;">Belum Absen</span>
                        @endif
                    </div>
                    <h4 style="font-size:17px;font-weight:700;color:#0f172a;margin:0 0 3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $jadwal->mapel->name ?? 'Mata Pelajaran' }}
                    </h4>
                    <p style="font-size:14px;color:#64748b;margin:0;display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                        <span>Guru: {{ $jadwal->guru->name ?? '-' }}</span>
                        @if(isset($jadwal->guru->no_telp) && $jadwal->guru->no_telp)
                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $jadwal->guru->no_telp)) }}" target="_blank" style="color:#64748b;font-size:12px;text-decoration:none;display:flex;align-items:center;gap:3px;background:#f8fafc;padding:2px 6px;border-radius:6px;border:1px solid #e2e8f0;"><i class="ti ti-brand-whatsapp" style="color:#16a34a;font-size:13px;"></i> {{ $jadwal->guru->no_telp }}</a>
                        @endif
                        <span>&nbsp;·&nbsp; {{ $jadwal->ruangan->name ?? '-' }}</span>
                    </p>
                </div>
            </div>

            @if($hasMasuk)
            <div style="display:flex;align-items:center;gap:10px;padding:7px 11px;background:#f8fafc;border-radius:9px;font-size:14px;border:1px solid #f1f5f9;">
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

            @if($canGenerate)
            <div>
                @if($isTimeToScan)
                    <button onclick="showQrModal('{{ $jadwal->id }}', '{{ addslashes($jadwal->mapel->name ?? 'Mapel') }}', 'masuk')"
                            class="s-btn">
                        <i class="ti ti-qrcode" style="font-size:17px;"></i> Generate QR MASUK
                    </button>
                @else
                    <div class="relative w-full" data-unlock-time="{{ $jadwal->jam_mulai }}">
                        <div class="s-btn-locked qr-locked-btn">
                            <i class="ti ti-lock" style="font-size:16px;"></i>
                            <span>Terbuka pukul <strong style="color:#475569;">{{ substr($jadwal->jam_mulai,0,5) }}</strong>
                                (<span class="qr-unlock-countdown" style="font-family:monospace;font-size:13px;">--:--</span>)
                            </span>
                        </div>
                        <button onclick="showQrModal('{{ $jadwal->id }}', '{{ addslashes($jadwal->mapel->name ?? 'Mapel') }}', 'masuk')"
                                class="s-btn qr-unlocked-btn" style="display:none;">
                            <i class="ti ti-qrcode" style="font-size:17px;"></i> Generate QR MASUK
                        </button>
                    </div>
                @endif
            </div>
            @else
            <div style="display:flex;align-items:center;justify-content:center;gap:6px;padding:8px;
                        background:#f0fdf4;border-radius:10px;border:1px solid #dcfce7;">
                <i class="ti ti-circle-check" style="color:#16a34a;font-size:17px;"></i>
                <span style="font-size:14px;font-weight:700;color:#15803d;">Selesai</span>
            </div>
            @endif
        </div>
        @empty
            @if($ongoingClasses->isEmpty())
            <div class="s-card" style="padding:36px 20px;text-align:center;">
                <div style="width:52px;height:52px;border-radius:16px;background:#f1f5f9;
                            display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                    <i class="ti ti-calendar-off" style="color:#94a3b8;font-size:26px;"></i>
                </div>
                <p style="font-weight:700;color:#0f172a;font-size:16px;margin:0 0 4px;">Tidak ada jadwal hari ini</p>
                <p style="color:#94a3b8;font-size:14px;margin:0;">Asyik, kelasmu sedang kosong!</p>
            </div>
            @endif
        @endforelse
    </div>
    <div class="mt-5 flex justify-center">{{ $jadwals->links() }}</div>
</div>

{{-- ══════════════════════════════════════════════════════ --}}
{{-- 3. SEMUA JADWAL + FILTER                              --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div style="margin-top:28px;">
    {{-- Filter --}}
    <form method="GET" action="{{ route('siswa.dashboard') }}"
          class="s-card" style="margin-bottom:18px;overflow:hidden;">
        <div style="display:flex;align-items:center;gap:7px;padding:10px 16px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            <i class="ti ti-filter" style="color:#64748b;font-size:15px;"></i>
            <span style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:1px;">Filter Data</span>
        </div>
        <div style="padding:14px 16px;display:flex;flex-direction:column;gap:10px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Tahun Ajaran</label>
                <select name="tahun_ajaran_id"
                        style="width:100%;background:#f8fafc;border:1px solid #e2e8f0;border-radius:11px;
                               padding:9px 13px;font-size:15px;color:#0f172a;outline:none;appearance:none;cursor:pointer;">
                    <option value="">— Semua Tahun Ajaran —</option>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}" {{ $selectedTahunAjaranId == $ta->id ? 'selected' : '' }}>
                            {{ $ta->name }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <div style="display:flex;gap:8px;align-items:center;">
                    <button type="submit" class="s-btn" style="flex:1;">
                        <i class="ti ti-filter" style="font-size:15px;"></i> Tampilkan
                    </button>
                    @if($selectedTahunAjaranId)
                    <a href="{{ route('siswa.dashboard') }}"
                       style="width:40px;height:40px;flex-shrink:0;background:#f8fafc;border:1px solid #e2e8f0;
                              border-radius:11px;display:flex;align-items:center;justify-content:center;
                              color:#64748b;text-decoration:none;transition:background .15s;">
                        <i class="ti ti-x" style="font-size:17px;"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- Section label --}}
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
        <p class="s-lbl" style="white-space:nowrap;">Semua Jadwal Kelas</p>
        <div style="flex:1;height:1px;background:#e2e8f0;"></div>
        @if($selectedTahunAjaran)
            <span style="background:#f1f5f9;border:1px solid #e2e8f0;color:#475569;font-size:12px;font-weight:700;padding:3px 10px;border-radius:99px;white-space:nowrap;">
                {{ $selectedTahunAjaran->name }}
            </span>
        @endif
        <span style="color:#cbd5e1;font-size:12px;white-space:nowrap;">{{ $jadwalSemua->flatten()->count() }} jadwal</span>
    </div>

    @if($jadwalSemua->isEmpty())
    <div class="s-card" style="padding:36px;text-align:center;">
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
                        <span class="{{ $namaHari === $hariIni ? 'day-t' : 'day-n' }}">
                            {{ $namaHari }}@if($namaHari === $hariIni)<span style="opacity:.7;margin-left:3px;">· Hari Ini</span>@endif
                        </span>
                        <div style="flex:1;height:1px;background:#f1f5f9;"></div>
                        <span style="font-size:12px;color:#cbd5e1;">{{ count($kelasHari) }} jadwal</span>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        @foreach($kelasHari as $j)
                        <div class="s-mini">
                            <div style="display:flex;align-items:center;justify-content:space-between;">
                                <span class="s-badge-t">{{ substr($j->jam_mulai,0,5) }} – {{ substr($j->jam_selesai,0,5) }}</span>
                            </div>
                            <h4 style="font-size:15px;font-weight:700;color:#0f172a;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $j->mapel->name ?? 'Mata Pelajaran' }}
                            </h4>
                            <p style="font-size:13px;color:#94a3b8;margin:0;display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
                                <span style="display:flex;align-items:center;gap:3px;"><i class="ti ti-user" style="font-size:13px;"></i> {{ $j->guru->name ?? '-' }}</span>
                                @if(isset($j->guru->no_telp) && $j->guru->no_telp)
                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $j->guru->no_telp)) }}" target="_blank" style="color:#94a3b8;font-size:11px;text-decoration:none;display:flex;align-items:center;gap:3px;background:#f8fafc;padding:2px 6px;border-radius:6px;border:1px solid #e2e8f0;"><i class="ti ti-brand-whatsapp" style="color:#16a34a;font-size:12px;"></i> {{ $j->guru->no_telp }}</a>
                                @endif
                                <span style="color:#e2e8f0;">·</span>
                                <span style="display:flex;align-items:center;gap:3px;"><i class="ti ti-door" style="font-size:13px;"></i> {{ $j->ruangan->name ?? '-' }}</span>
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>

{{-- ══════════════════════════════════════════════════════ --}}
{{-- QR MODAL                                              --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div id="qr-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeQrModal()"></div>
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-sm relative z-10 overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="qr-modal-content">
        <div class="p-6 text-center flex flex-col items-center justify-center h-full">
            <h3 id="qr-mapel-name" style="font-weight:800;font-size:20px;color:#0f172a;margin:0 0 4px;">Nama Mapel</h3>
            <p style="font-size:14px;color:#94a3b8;margin:0 0 20px;">Tunjukkan QR ini ke Guru yang bersangkutan</p>
            <div style="display:flex;justify-content:center;margin-bottom:14px;">
                <div id="qr-container" style="padding:14px;background:#fff;border:3px solid #f1f5f9;border-radius:16px;display:inline-block;">
                    <div id="qrcode"></div>
                </div>
            </div>
            <p id="qr-countdown" style="font-size:13px;color:#64748b;font-weight:600;margin:0 0 18px;">QR diperbarui dalam 30s</p>
            <button id="fullscreen-btn" onclick="requestFullscreenQr()"
                    style="width:100%;background:#0f172a;color:#fff;border:none;border-radius:12px;padding:12px;font-size:15px;font-weight:700;cursor:pointer;margin-bottom:8px;">
                Layar Penuh
            </button>
            <button id="close-btn" onclick="closeQrModal()"
                    style="width:100%;background:#f8fafc;color:#475569;border:1px solid #e2e8f0;border-radius:12px;padding:12px;font-size:15px;font-weight:700;cursor:pointer;">
                Tutup
            </button>
        </div>
    </div>
</div>

<style>
    #qr-modal-content:fullscreen {
        width:100vw!important;height:100vh!important;max-width:none!important;
        display:flex!important;flex-direction:column!important;
        justify-content:center!important;align-items:center!important;
        border-radius:0!important;background:#fff!important;padding:2rem!important;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    let qrcodeObj=null,qrIntervalId=null,countdownIntervalId=null,pollIntervalId=null;
    let currentJadwalId=null,currentMapelName=null,currentQrType=null,countdownSeconds=30;

    function showQrModal(id,name,type){
        const modal=document.getElementById('qr-modal'),mc=document.getElementById('qr-modal-content');
        currentJadwalId=id;currentMapelName=name;currentQrType=type;
        document.getElementById('qr-mapel-name').innerText=name;
        renderQrCode();startQrTimers();
        modal.classList.remove('hidden');modal.classList.add('flex');
        setTimeout(()=>{mc.classList.remove('scale-95','opacity-0');mc.classList.add('scale-100','opacity-100');},10);
    }
    function renderQrCode(){
        const el=document.getElementById('qrcode');el.innerHTML='';
        const fs=document.fullscreenElement===document.getElementById('qr-modal-content');
        new QRCode(el,{text:JSON.stringify({type:'absen_jadwal',jadwal_id:currentJadwalId,timestamp:Date.now()}),
            width:fs?320:220,height:fs?320:220,colorDark:"#0f172a",colorLight:"#ffffff",correctLevel:QRCode.CorrectLevel.H});
    }
    function startQrTimers(){
        stopQrTimers();countdownSeconds=30;updateCountdownText();
        qrIntervalId=setInterval(()=>{renderQrCode();countdownSeconds=30;updateCountdownText();},30000);
        countdownIntervalId=setInterval(()=>{countdownSeconds--;if(countdownSeconds<0)countdownSeconds=30;updateCountdownText();},1000);
        pollIntervalId=setInterval(()=>{
            fetch(`/siswa/jadwal/${currentJadwalId}/status`).then(r=>r.json()).then(d=>{
                if((currentQrType==='masuk'&&d.absen_masuk)||(currentQrType==='keluar'&&d.absen_keluar)){
                    const msg=currentQrType==='masuk'?'Guru telah scan QR MASUK.':'Guru telah scan QR KELUAR.';
                    if(typeof Swal!=='undefined'){Swal.fire('Berhasil!',msg,'success').then(()=>location.reload());}
                    else{alert('Berhasil! '+msg);location.reload();}
                }
            }).catch(console.error);
        },3000);
    }
    function stopQrTimers(){
        if(qrIntervalId){clearInterval(qrIntervalId);qrIntervalId=null;}
        if(countdownIntervalId){clearInterval(countdownIntervalId);countdownIntervalId=null;}
        if(pollIntervalId){clearInterval(pollIntervalId);pollIntervalId=null;}
    }
    function updateCountdownText(){const e=document.getElementById('qr-countdown');if(e)e.innerText=`QR diperbarui dalam ${countdownSeconds}s`;}
    function requestFullscreenQr(){const e=document.getElementById('qr-modal-content');if(e.requestFullscreen)e.requestFullscreen();}
    function closeQrModal(){
        const modal=document.getElementById('qr-modal'),mc=document.getElementById('qr-modal-content');
        stopQrTimers();mc.classList.remove('scale-100','opacity-100');mc.classList.add('scale-95','opacity-0');
        setTimeout(()=>{modal.classList.remove('flex');modal.classList.add('hidden');},300);
    }
    document.addEventListener('fullscreenchange',()=>{
        const mc=document.getElementById('qr-modal-content'),fs=document.fullscreenElement===mc;
        const fb=document.getElementById('fullscreen-btn'),cb=document.getElementById('close-btn');
        if(fs){if(fb)fb.style.display='none';if(cb)cb.style.display='none';}
        else{if(fb)fb.style.display='block';if(cb)cb.style.display='block';}
        renderQrCode();
    });

    // Countdown kelas berikutnya
    const tgt=@json($nextJamMulai),cEl=document.getElementById('next-class-countdown');
    if(tgt&&cEl){
        function tick(){
            const now=new Date(),p=tgt.split(':'),t=new Date();
            t.setHours(+p[0],+p[1],+(p[2]||0),0);
            const d=Math.floor((t-now)/1000);
            if(d<=0){cEl.textContent='00:00:00';return;}
            cEl.textContent=[Math.floor(d/3600),Math.floor((d%3600)/60),d%60].map(n=>String(n).padStart(2,'0')).join(':');
        }
        tick();setInterval(tick,1000);
    }

    // Unlock QR button countdown
    const lcs=document.querySelectorAll('[data-unlock-time]');
    if(lcs.length>0){
        function pad(n){return String(n).padStart(2,'0');}
        function chk(){
            const now=new Date();
            lcs.forEach(c=>{
                const p=c.getAttribute('data-unlock-time').split(':'),t=new Date();
                t.setHours(+p[0],+p[1],0,0);
                const d=Math.floor((t-now)/1000);
                const lb=c.querySelector('.qr-locked-btn'),ub=c.querySelector('.qr-unlocked-btn'),ce=c.querySelector('.qr-unlock-countdown');
                if(d<=0){if(lb)lb.style.display='none';if(ub)ub.style.display='flex';}
                else if(ce){ce.textContent=pad(Math.floor(d/60))+':'+pad(d%60);}
            });
        }
        chk();setInterval(chk,1000);
    }
</script>
@endif

@endsection
