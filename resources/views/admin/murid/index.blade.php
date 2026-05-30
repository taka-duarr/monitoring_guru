@extends('layouts.admin')
@section('title', 'Manajemen Murid - ' . $kelas->name)
@section('page_title', 'Data Murid Kelas ' . $kelas->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('kelas.index') }}" class="text-slate-500 hover:text-slate-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Daftar Kelas
    </a>
</div>

<div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-100">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Daftar Murid</h3>
            <p class="text-sm text-slate-500 mt-0.5">Kelas: <strong>{{ $kelas->name }}</strong> (Total: {{ $murids->count() }} siswa)</p>
        </div>
        <button type="button" onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition-colors shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Murid
        </button>
    </div>
    
    @if(session('success'))
    <div class="bg-green-50 text-green-700 p-4 border-b border-green-100">
        {{ session('success') }}
    </div>
    @endif
    
    @if($errors->any())
    <div class="bg-red-50 text-red-700 p-4 border-b border-red-100">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">No Absen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">NIS</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Lengkap</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($murids as $row)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-800">{{ $row->no_absen ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-800">{{ $row->nis }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ $row->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                        <button type="button" onclick="editMurid('{{ $row->id }}', '{{ $row->no_absen }}', '{{ $row->nis }}', '{{ addslashes($row->name) }}')" class="text-blue-600 hover:text-blue-800 font-semibold">Edit</button>
                        <form method="POST" action="{{ route('kelas.murid.destroy', [$kelas->id, $row->id]) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus data siswa ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">Hapus</button>
                        </form>
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

<!-- Modal Tambah -->
<div id="modalTambah" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('modalTambah').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-slate-100">
                <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">Tambah Murid Baru</h3>
            </div>
            <form action="{{ route('kelas.murid.store', $kelas->id) }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">No Absen</label>
                        <input type="number" name="no_absen" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">NIS (Nomor Induk Siswa)</label>
                        <input type="text" name="nis" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all">
                    </div>
                </div>
                <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl">
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-brand-600 text-base font-medium text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Simpan Data
                    </button>
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('modalEdit').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-slate-100">
                <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">Edit Data Murid</h3>
            </div>
            <form id="formEdit" method="POST">
                @csrf @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">No Absen</label>
                        <input type="number" name="no_absen" id="edit_no_absen" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">NIS (Nomor Induk Siswa)</label>
                        <input type="text" name="nis" id="edit_nis" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" id="edit_name" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all">
                    </div>
                </div>
                <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl">
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Update Data
                    </button>
                    <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editMurid(id, no_absen, nis, name) {
        document.getElementById('edit_no_absen').value = no_absen;
        document.getElementById('edit_nis').value = nis;
        document.getElementById('edit_name').value = name;
        document.getElementById('formEdit').action = "/admin/kelas/{{ $kelas->id }}/murid/" + id;
        document.getElementById('modalEdit').classList.remove('hidden');
    }
</script>
@endsection
