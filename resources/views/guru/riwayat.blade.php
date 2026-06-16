@extends('layouts.guru')
@section('title', 'Riwayat Mengajar')

@section('content')
<div class="p-5">
    <div class="mb-6">
        <h2 class="text-2xl font-heading font-bold text-slate-800">Riwayat Mengajar</h2>
        <p class="text-slate-500 mt-1">Seluruh rekaman absensi mengajar Anda, difilter per tahun ajaran.</p>
    </div>

    {{-- FILTER TAHUN AJARAN --}}
    <form method="GET" action="{{ route('guru.riwayat') }}"
          class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-2">
            <i class="ti ti-filter"></i> Filter Data
        </p>
        <div class="flex flex-col md:flex-row gap-3 items-end">
            <div class="flex-1 min-w-0">
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-700 focus:ring-2 focus:ring-brand-300 focus:border-brand-400 outline-none transition">
                    <option value="">-- Semua Tahun Ajaran --</option>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}" {{ $selectedId == $ta->id ? 'selected' : '' }}>
                            {{ $ta->name }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 shrink-0">
                <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl transition flex items-center gap-2">
                    <i class="ti ti-filter text-sm"></i> Filter
                </button>
                @if($selectedId)
                    <a href="{{ route('guru.riwayat') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-xl transition flex items-center gap-2">
                        <i class="ti ti-x text-sm"></i> Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Info hasil --}}
    @if($selectedTahunAjaran)
        <div class="mb-4 px-4 py-2.5 bg-brand-50 border border-brand-200 rounded-xl text-sm text-brand-700 flex items-center gap-2">
            <i class="ti ti-calendar-event"></i>
            Menampilkan riwayat Tahun Ajaran <strong>{{ $selectedTahunAjaran->name }}</strong>
            — <strong>{{ $riwayat->total() }}</strong> sesi mengajar
        </div>
    @endif

    {{-- LIST RIWAYAT --}}
    <div class="space-y-3">
        @forelse($riwayat as $absen)
        @php
            $hasKeluar = \App\Models\AbsenKeluar::where('absen_masuk_id', $absen->id)->first();
            $mapelName = $absen->jadwalAjar?->mapel?->name ?? 'Mata Pelajaran';
            $ta = $absen->jadwalAjar?->tahunAjaran;
        @endphp
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3 hover:shadow-md transition">
            {{-- Kiri: info utama --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                    <span class="text-xs font-bold text-brand-600 bg-brand-50 px-2 py-1 rounded-md">
                        {{ \Carbon\Carbon::parse($absen->tanggal)->isoFormat('ddd, D MMM YYYY') }}
                    </span>
                    @if($hasKeluar)
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-md">Selesai</span>
                    @else
                        <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-1 rounded-md">Belum Selesai</span>
                    @endif
                    @if($ta && !$selectedTahunAjaran)
                        <span class="text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded-md">{{ $ta->name }}</span>
                    @endif
                </div>
                <p class="font-bold text-slate-800 text-sm leading-snug">{{ $mapelName }}</p>
                <p class="text-xs text-slate-500 mt-0.5">
                    Kelas {{ $absen->kelas ? (($absen->kelas->grade ? $absen->kelas->grade . ' ' : '') . $absen->kelas->name) : '-' }}
                    • {{ $absen->ruangan?->name ?? '-' }}
                </p>
            </div>

            {{-- Tengah: jam --}}
            <div class="flex gap-4 px-4 py-2.5 bg-slate-50 rounded-xl border border-slate-100 text-sm shrink-0">
                <div class="text-center">
                    <p class="text-xs text-slate-400">Masuk</p>
                    <p class="font-bold text-slate-700 font-mono">{{ substr($absen->jam_masuk, 0, 5) }}</p>
                </div>
                <div class="w-px bg-slate-200"></div>
                <div class="text-center">
                    <p class="text-xs text-slate-400">Keluar</p>
                    <p class="font-bold text-slate-700 font-mono">{{ $hasKeluar ? substr($hasKeluar->jam_keluar, 0, 5) : '--:--' }}</p>
                </div>
            </div>

            {{-- Kanan: aksi --}}
            <div class="flex gap-2 shrink-0">
                @if($absen->jadwal_ajar_id)
                    <a href="{{ route('guru.riwayat_jadwal', $absen->jadwal_ajar_id) }}"
                       class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold px-3 py-2 rounded-lg transition flex items-center gap-1.5">
                        <i class="ti ti-book text-sm"></i> Detail Kelas
                    </a>
                @endif
                <a href="{{ route('guru.absen_murid', $absen->id) }}"
                   class="text-xs bg-brand-50 hover:bg-brand-100 text-brand-600 font-semibold px-3 py-2 rounded-lg transition flex items-center gap-1.5">
                    <i class="ti ti-users text-sm"></i> Absen Murid
                </a>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl p-10 text-center shadow-sm border border-slate-100">
            <i class="ti ti-calendar-off text-5xl text-slate-300 mb-3 block"></i>
            <p class="text-slate-500 font-medium">Belum ada riwayat mengajar untuk tahun ajaran ini.</p>
            @if($selectedId)
                <a href="{{ route('guru.riwayat') }}" class="mt-3 inline-block text-sm text-brand-600 hover:underline">Tampilkan semua riwayat</a>
            @endif
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($riwayat->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $riwayat->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
