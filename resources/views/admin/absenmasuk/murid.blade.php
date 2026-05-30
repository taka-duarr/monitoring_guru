@extends('layouts.admin')
@section('title', 'Absensi Murid - Monitoring Guru')
@section('page_title', 'Data Kehadiran Murid')

@section('content')
<div class="mb-6">
    <a href="{{ route('absenmasuk.index') }}" class="text-slate-500 hover:text-slate-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Rekap Absensi
    </a>
</div>

<div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-100">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Daftar Hadir Murid</h3>
            <p class="text-sm text-slate-500 mt-0.5">
                Tanggal: <strong>{{ \Carbon\Carbon::parse($absenmasuk->tanggal)->translatedFormat('d M Y') }}</strong><br>
                Guru: <strong>{{ $absenmasuk->guru->name ?? '-' }}</strong> | Kelas: <strong>{{ $absenmasuk->kelas->name ?? '-' }}</strong><br>
                Mata Pelajaran: <strong>{{ $absenmasuk->jadwalAjar->mapel->name ?? '-' }}</strong>
            </p>
        </div>
        <div class="bg-indigo-50 px-4 py-2 rounded-xl border border-indigo-100">
            <p class="text-sm font-medium text-indigo-700">Total Siswa: {{ $murids->count() }}</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">No Absen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">NIS</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Lengkap</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Status Kehadiran</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($murids as $row)
                @php
                    // Default hadir jika belum ada record. Jika ada, pakai status di tabel absen_murids
                    $status = 'hadir';
                    if ($absenMurids->has($row->id)) {
                        $status = $absenMurids[$row->id]->status;
                    }
                @endphp
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-800">{{ $row->no_absen ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $row->nis }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ $row->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                        @if($status == 'hadir')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-xs font-bold uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            Hadir
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-red-50 text-red-700 text-xs font-bold uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Alpa
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-16 text-center text-slate-400">
                        <svg class="w-14 h-14 mx-auto text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <p class="font-semibold text-slate-500">Belum ada murid di kelas ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
