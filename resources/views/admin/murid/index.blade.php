@extends('layouts.admin')

@section('title', 'Manajemen Murid - ' . $kelas->name . ' - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')
<div x-data="{ 
    showTambah: false, 
    showEdit: false, 
    editId: '', 
    editNoAbsen: '', 
    editNis: '', 
    editName: '' 
}" class="position-relative">

    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Siswa Kelas {{ $kelas->name }}</h2>
            <p class="text-sm text-neutral-500">Total terdaftar: <strong>{{ $murids->count() }}</strong> siswa</p>
        </div>
        <div class="d-flex align-center gap-2">
            <a href="{{ route('kelas.index') }}" class="btn btn-secondary d-flex align-center gap-2" style="text-decoration: none;">
                <i class="ti ti-arrow-left"></i> Kembali
            </a>
            <button type="button" class="btn btn-primary d-flex align-center gap-2" @click="showTambah = true">
                <i class="ti ti-plus"></i> Tambah Murid
            </button>
        </div>
    </div>

    <!-- MAIN DATA TABLE SECTION -->
    <div class="table-wrapper card p-0 overflow-hidden" x-data="{ tableLoading: false }">
        <div class="table-loading-overlay" x-show="tableLoading" style="display: none;">
            <div class="table-spinner"></div>
        </div>

        @if($murids->isEmpty())
            <div class="table-empty-state">
                <div class="table-empty-icon">
                    <i class="ti ti-users text-primary"></i>
                </div>
                <span class="table-empty-title">Belum ada data murid</span>
                <span class="table-empty-sub">Silakan tambahkan siswa baru ke dalam kelas ini.</span>
                <div class="table-empty-actions">
                    <button type="button" class="btn btn-primary" @click="showTambah = true">Tambah Murid Baru</button>
                </div>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="col-no">No Absen</th>
                        <th>NIS</th>
                        <th>Nama Lengkap</th>
                        <th class="col-actions col-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($murids as $row)
                        <tr>
                            <td class="col-no font-semibold">
                                {{ $row->no_absen ?? '-' }}
                            </td>
                            <td>
                                <span class="font-mono text-xs">{{ $row->nis }}</span>
                            </td>
                            <td>
                                <span class="font-bold text-neutral-800">{{ $row->name }}</span>
                            </td>
                            <td class="col-actions col-center">
                                <div class="action-buttons-group">
                                    <!-- Edit Record -->
                                    <x-tooltip text="Edit Data">
                                        <button type="button" class="btn btn-ghost action-edit"
                                                @click="showEdit = true; 
                                                        editId = '{{ $row->id }}'; 
                                                        editNoAbsen = '{{ $row->no_absen }}'; 
                                                        editNis = '{{ $row->nis }}'; 
                                                        editName = '{{ addslashes($row->name) }}'">
                                            <i class="ti ti-pencil"></i>
                                        </button>
                                    </x-tooltip>
                                    
                                    <!-- Delete Button -->
                                    <x-tooltip text="Hapus Data">
                                        <button type="button" class="btn btn-ghost action-delete" 
                                                @click="$dispatch('confirm-delete', {
                                                    url: '{{ route('kelas.murid.destroy', [$kelas->id, $row->id]) }}',
                                                    name: 'Siswa {{ addslashes($row->name) }}'
                                                })">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </x-tooltip>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Reusable Hapus Modal -->
    <x-modal-hapus />

    <!-- MODAL TAMBAH INLINE -->
    <div class="modal-backdrop-blur" x-show="showTambah" x-transition style="display: none; position: fixed; inset: 0; z-index: 100; background-color: rgba(15, 23, 42, 0.6); align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div class="card shadow-lg" style="width: 100%; max-width: 500px; padding: 0; overflow: hidden; border-radius: var(--radius-lg);" @click.away="showTambah = false">
            <div class="card-header d-flex align-center justify-between" style="background-color: var(--color-primary-900); color: white; padding: 16px 24px;">
                <h3 class="card-title text-white" style="margin: 0; font-size: 16px; font-weight: 700;">Tambah Murid Baru</h3>
                <button type="button" class="btn btn-ghost text-white p-1" style="min-width: unset; height: unset; color: white !important;" @click="showTambah = false">
                    <i class="ti ti-x" style="font-size: 20px;"></i>
                </button>
            </div>
            <form action="{{ route('kelas.murid.store', $kelas->id) }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
                @csrf
                <div style="padding: 24px;">
                    <!-- No Absen -->
                    <div class="form-group">
                        <label for="no_absen" class="form-label">No. Absen</label>
                        <input type="number" id="no_absen" name="no_absen" class="form-control" placeholder="Contoh: 1, 2, 15...">
                    </div>

                    <!-- NIS -->
                    <div class="form-group">
                        <label for="nis" class="form-label">NIS (Nomor Induk Siswa) <span class="required-indicator">*</span></label>
                        <input type="text" id="nis" name="nis" class="form-control" placeholder="Masukkan NIS siswa..." required>
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="form-group mb-0">
                        <label for="name" class="form-label">Nama Lengkap <span class="required-indicator">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan nama lengkap siswa..." required>
                    </div>
                </div>
                <div class="d-flex justify-end gap-3" style="background-color: var(--color-neutral-50); padding: 16px 24px; border-top: 1px solid var(--color-neutral-200);">
                    <button type="button" class="btn btn-secondary" @click="showTambah = false" :disabled="loading">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-center gap-2" :disabled="loading">
                        <template x-if="loading">
                            <span class="table-spinner" style="width: 14px; height: 14px; border-width: 2px; border-color: white; border-top-color: transparent;"></span>
                        </template>
                        <span x-text="loading ? 'Menyimpan...' : 'Simpan Data'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT INLINE -->
    <div class="modal-backdrop-blur" x-show="showEdit" x-transition style="display: none; position: fixed; inset: 0; z-index: 100; background-color: rgba(15, 23, 42, 0.6); align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div class="card shadow-lg" style="width: 100%; max-width: 500px; padding: 0; overflow: hidden; border-radius: var(--radius-lg);" @click.away="showEdit = false">
            <div class="card-header d-flex align-center justify-between" style="background-color: var(--color-primary-900); color: white; padding: 16px 24px;">
                <h3 class="card-title text-white" style="margin: 0; font-size: 16px; font-weight: 700;">Edit Data Murid</h3>
                <button type="button" class="btn btn-ghost text-white p-1" style="min-width: unset; height: unset; color: white !important;" @click="showEdit = false">
                    <i class="ti ti-x" style="font-size: 20px;"></i>
                </button>
            </div>
            <form :action="'/admin/kelas/{{ $kelas->id }}/murid/' + editId" method="POST" x-data="{ loading: false }" @submit="loading = true">
                @csrf
                @method('PUT')
                <div style="padding: 24px;">
                    <!-- No Absen -->
                    <div class="form-group">
                        <label for="edit_no_absen" class="form-label">No. Absen</label>
                        <input type="number" id="edit_no_absen" name="no_absen" class="form-control" x-model="editNoAbsen">
                    </div>

                    <!-- NIS -->
                    <div class="form-group">
                        <label for="edit_nis" class="form-label">NIS (Nomor Induk Siswa) <span class="required-indicator">*</span></label>
                        <input type="text" id="edit_nis" name="nis" class="form-control" x-model="editNis" required>
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="form-group mb-0">
                        <label for="edit_name" class="form-label">Nama Lengkap <span class="required-indicator">*</span></label>
                        <input type="text" id="edit_name" name="name" class="form-control" x-model="editName" required>
                    </div>
                </div>
                <div class="d-flex justify-end gap-3" style="background-color: var(--color-neutral-50); padding: 16px 24px; border-top: 1px solid var(--color-neutral-200);">
                    <button type="button" class="btn btn-secondary" @click="showEdit = false" :disabled="loading">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-center gap-2" :disabled="loading">
                        <template x-if="loading">
                            <span class="table-spinner" style="width: 14px; height: 14px; border-width: 2px; border-color: white; border-top-color: transparent;"></span>
                        </template>
                        <span x-text="loading ? 'Menyimpan...' : 'Simpan Data'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
