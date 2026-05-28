@extends('layouts.admin')
@section('title', 'Status Kelas - Monitoring Guru')
@section('page_title', 'Data Status Kelas')

@section('content')
<div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-100">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Daftar Status Kelas</h3>
            <p class="text-sm text-slate-500 mt-0.5">Total: <strong>{{ $data->total() }}</strong> data</p>
        </div>
        <a href="{{ route('statuskelas.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition-colors shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Data
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Ruangan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Mapel</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Pengajar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status (Live)</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($data as $row)
                @php
                    $status = $row->live_status;
                    $isActive = $status ? $status->is_active : false;
                @endphp
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $loop->iteration + ($data->firstItem() - 1) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800 font-bold">{{ $row->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ $isActive ? ($status->ruangan ?? '-') : '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $isActive ? 'text-slate-700' : 'text-slate-300 italic' }}">
                        {{ $isActive ? ($status->mapel ?? '-') : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $isActive ? 'text-slate-700' : 'text-slate-300 italic' }}">
                        {{ $isActive ? ($status->pengajar ?? '-') : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                        @if($isActive)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider rounded-md border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Sedang Belajar
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider rounded-md border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Kosong
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                        @if($status)
                        <a href="{{ route('statuskelas.edit', $status->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Edit Log</a>
                        <form method="POST" action="{{ route('statuskelas.destroy', $status->id) }}" class="inline" onsubmit="return confirm('Hapus data log kelas ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">Reset</button>
                        </form>
                        @else
                        <span class="text-slate-300 italic text-xs">Belum ada aktivitas</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="100%" class="px-6 py-16 text-center text-slate-400">
                        <svg class="w-14 h-14 mx-auto text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="font-semibold text-slate-500">Belum ada data Status Kelas</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100">
        {{ $data->links() }}
    </div>
</div>
@endsection