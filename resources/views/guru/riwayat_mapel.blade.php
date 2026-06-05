@extends('layouts.guru')
@section('title', 'Riwayat Mengajar - ' . $mapel->name)

@section('content')
<div class="p-5">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('guru.dashboard') }}" class="w-10 h-10 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-heading font-bold text-slate-800">Riwayat {{ $mapel->name }}</h2>
            <p class="text-slate-500 mt-1">Daftar absensi masuk kelas Anda.</p>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($riwayat as $absen)
        @php
            $hasKeluar = \App\Models\AbsenKeluar::where('absen_masuk_id', $absen->id)->first();
        @endphp
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-1 bg-brand-50 text-brand-700 text-xs font-bold rounded-lg">{{ \Carbon\Carbon::parse($absen->tanggal)->isoFormat('dddd, D MMMM YYYY') }}</span>
                    @if($hasKeluar)
                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg">Selesai</span>
                    @else
                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg">Sedang Mengajar</span>
                    @endif
                </div>
                <h3 class="font-bold text-lg text-slate-800">Kelas {{ $absen->kelas->name ?? '-' }}</h3>
                <p class="text-sm text-slate-500">{{ $absen->ruangan->name ?? '-' }}</p>
            </div>
            
            <div class="flex gap-4 p-3 bg-slate-50 rounded-xl border border-slate-100">
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Masuk</p>
                    <p class="font-bold text-slate-700">{{ $absen->jam_masuk }}</p>
                </div>
                <div class="w-px bg-slate-200"></div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Keluar</p>
                    <p class="font-bold text-slate-700">{{ $hasKeluar ? $hasKeluar->jam_keluar : '-' }}</p>
                </div>
            </div>
            
            <div class="shrink-0 flex items-center">
                <a href="{{ route('guru.absen_murid', $absen->id) }}" class="bg-brand-50 hover:bg-brand-100 text-brand-600 font-semibold px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Absen Murid
                </a>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl p-8 text-center shadow-sm border border-slate-100">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-slate-500 font-medium">Belum ada riwayat mengajar untuk mata pelajaran ini.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
