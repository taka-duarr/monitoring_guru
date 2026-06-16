@extends('layouts.guru')
@section('title', 'Riwayat Mengajar - ' . ($jadwal->mapel->name ?? 'Mapel'))

@section('content')
<div class="p-5">
    <div class="mb-6 flex items-start gap-4">
        <a href="{{ route('guru.dashboard') }}" class="w-10 h-10 shrink-0 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition">
            <i class="ti ti-arrow-left text-xl"></i>
        </a>
        <div>
            <h2 class="text-2xl font-heading font-bold text-slate-800 flex items-center gap-2">
                {{ $jadwal->mapel->name ?? 'Mata Pelajaran' }}
            </h2>
            <div class="mt-2 flex items-center gap-3 text-sm text-slate-500 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 rounded-md font-medium text-slate-600">
                    <i class="ti ti-school"></i>
                    Kelas {{ $jadwal->kelas ? (($jadwal->kelas->grade ? $jadwal->kelas->grade . ' ' : '') . $jadwal->kelas->name) : '-' }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 rounded-md font-medium text-slate-600">
                    <i class="ti ti-calendar-stats"></i>
                    Tahun Ajaran {{ $jadwal->tahunAjaran->name ?? '-' }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 rounded-md font-medium text-slate-600">
                    <i class="ti ti-door"></i>
                    Ruangan {{ $jadwal->ruangan->name ?? '-' }}
                </span>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($riwayat as $absen)
        @php
            $hasKeluar = \App\Models\AbsenKeluar::where('absen_masuk_id', $absen->id)->first();
        @endphp
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    @if($hasKeluar)
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg">Selesai</span>
                    @else
                        <span class="px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg">Belum Selesai</span>
                    @endif
                </div>
                <h3 class="font-bold text-lg text-slate-800">
                    {{ \Carbon\Carbon::parse($absen->tanggal)->isoFormat('dddd, D MMMM YYYY') }}
                </h3>
            </div>
            <div class="flex gap-4 p-3 bg-slate-50 rounded-xl border border-slate-100 shrink-0">
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Masuk</p>
                    <p class="font-bold text-slate-700">{{ substr($absen->jam_masuk, 0, 5) }}</p>
                </div>
                <div class="w-px bg-slate-200"></div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Keluar</p>
                    <p class="font-bold text-slate-700">{{ $hasKeluar ? substr($hasKeluar->jam_keluar, 0, 5) : '-' }}</p>
                </div>
            </div>
            <div class="shrink-0">
                <a href="{{ route('guru.absen_murid', $absen->id) }}" class="bg-brand-50 hover:bg-brand-100 text-brand-600 font-semibold px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                    <i class="ti ti-users text-sm"></i> Absen Murid
                </a>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl p-8 text-center shadow-sm border border-slate-100">
            <i class="ti ti-calendar-off text-5xl text-slate-300 mb-3 block"></i>
            <p class="text-slate-500 font-medium">Belum ada riwayat mengajar untuk jadwal kelas ini.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
