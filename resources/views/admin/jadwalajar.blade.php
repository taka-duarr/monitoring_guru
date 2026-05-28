@extends('layouts.admin')
@section('title', 'Jadwal Ajar - Monitoring Guru')
@section('page_title', 'Data Jadwal Ajar')

@section('content')
<div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-100">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Daftar Jadwal Ajar</h3>
            <p class="text-sm text-slate-500 mt-0.5">Total: <strong>{{ $data->total() }}</strong> data</p>
        </div>
        <a href="{{ route('jadwalajar.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition-colors shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Data
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Hari</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Guru</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Mapel</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Ruangan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jam Mulai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jam Selesai</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($data as $row)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $loop->iteration + ($data->firstItem() - 1) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-700">{{ $row->hari }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ $row->guru->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ $row->mapel->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ $row->kelas->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ $row->ruangan->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ $row->jam_mulai }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ $row->jam_selesai }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                        <a href="{{ route('jadwalajar.edit', $row->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Edit</a>
                        <form method="POST" action="{{ route('jadwalajar.destroy', $row->id) }}" class="inline" onsubmit="return confirm('Hapus data ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="100%" class="px-6 py-16 text-center text-slate-400">
                        <svg class="w-14 h-14 mx-auto text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="font-semibold text-slate-500">Belum ada data Jadwal Ajar</p>
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