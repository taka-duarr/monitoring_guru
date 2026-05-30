@extends('layouts.guru')
@section('title', 'Jadwal Mengajar')

@section('content')
<div class="p-5">
    <div class="mb-6">
        <h2 class="text-2xl font-heading font-bold text-slate-800">Halo, {{ Auth::user()->name }}! 👋</h2>
        <p class="text-slate-500 mt-1">Berikut adalah jadwal mengajarmu hari ini.</p>
    </div>

    <div class="space-y-4">
        @forelse($jadwals as $jadwal)
        @php
            $hasMasuk = $jadwal->absen_masuk !== null;
            $hasKeluar = $jadwal->absen_keluar !== null;
            
            // Tentukan status dan warna
            if ($hasMasuk && $hasKeluar) {
                $statusText = 'Selesai';
                $statusColor = 'bg-emerald-100 text-emerald-700';
                $borderClass = 'border-emerald-200';
                $canScan = false;
            } elseif ($hasMasuk && !$hasKeluar) {
                $statusText = 'Sedang Mengajar';
                $statusColor = 'bg-amber-100 text-amber-700';
                $borderClass = 'border-amber-200';
                $canScan = true; // Bisa scan untuk KELUAR
            } else {
                $statusText = 'Belum Absen';
                $statusColor = 'bg-slate-100 text-slate-500';
                $borderClass = 'border-slate-100';
                $canScan = true; // Bisa scan untuk MASUK
            }
        @endphp
        
        <div class="bg-white rounded-2xl p-5 shadow-sm border {{ $borderClass }} relative overflow-hidden transition-all">
            @if($hasMasuk && !$hasKeluar)
            <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-400"></div>
            @elseif($hasMasuk && $hasKeluar)
            <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-500"></div>
            @endif
            
            <div class="flex justify-between items-start mb-3">
                <div>
                    <span class="inline-block px-2.5 py-1 {{ $statusColor }} text-[10px] font-bold uppercase tracking-wider rounded-md mb-2">
                        {{ $statusText }}
                    </span>
                    <h3 class="font-bold text-lg text-slate-800 leading-tight">
                        <a href="{{ route('guru.riwayat_mapel', $jadwal->mapel_id) }}" class="hover:text-brand-600 hover:underline transition">
                            {{ $jadwal->mapel->name ?? 'Mata Pelajaran' }}
                        </a>
                    </h3>
                    <p class="text-sm text-slate-500 mt-0.5">Kelas {{ $jadwal->kelas->name ?? '-' }} • {{ $jadwal->ruangan->name ?? 'Ruang Belum Diset' }}</p>
                </div>
                <div class="text-right bg-slate-50 px-3 py-2 rounded-xl border border-slate-100">
                    <p class="text-sm font-bold text-slate-800">{{ $jadwal->jam_mulai }}</p>
                    <p class="text-xs text-slate-400">s/d {{ $jadwal->jam_selesai }}</p>
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
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span class="text-slate-600 font-medium">Keluar: <strong class="text-slate-800">{{ $jadwal->absen_keluar->jam_keluar }}</strong></span>
                </div>
                @else
                <div class="flex items-center gap-1.5 opacity-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-slate-500">Belum Keluar</span>
                </div>
                @endif
            </div>
            @endif
            
            <!-- Action Buttons -->
            @if($canScan)
            <div class="mt-4 flex gap-2">
                <a href="{{ route('guru.scan') }}" class="flex-1 {{ $hasMasuk ? 'bg-amber-500 hover:bg-amber-600' : 'bg-brand-500 hover:bg-brand-600' }} text-white text-center py-3 rounded-xl text-sm font-semibold transition shadow-sm">
                    {{ $hasMasuk ? 'Scan QR KELUAR' : 'Scan QR MASUK' }}
                </a>
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white rounded-2xl p-8 text-center shadow-sm border border-slate-100">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-slate-500 font-medium">Hore! Tidak ada jadwal mengajar di hari {{ $hariIni }} ini.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
