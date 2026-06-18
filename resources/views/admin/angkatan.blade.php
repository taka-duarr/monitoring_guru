@extends('layouts.admin')

@section('title', 'Manajemen Angkatan - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')
<div x-data="{ 
    showTambah: false, 
    showEdit: false, 
    editId: '', 
    editName: '' 
}" class="position-relative">

    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Data Angkatan</h2>
            <p class="text-sm text-neutral-500">Kelola kelompok tahun masuk siswa (angkatan) sekolah</p>
        </div>
        <div class="d-flex gap-3 align-center">
            <!-- Live Search -->
            <form action="{{ route('angkatan.index') }}" method="GET" class="m-0" @submit.prevent>
                <div class="position-relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="form-control live-search-input pl-10" 
                           placeholder="Cari angkatan..." style="padding-left: 2.5rem; width: 250px;">
                    <i class="ti ti-search position-absolute text-neutral-400" style="left: 0.75rem; top: 50%; transform: translateY(-50%);"></i>
                </div>
            </form>
            
            <button type="button" class="btn btn-primary d-flex align-center gap-2" @click="showTambah = true">
                <i class="ti ti-plus"></i> Tambah Angkatan
            </button>
        </div>
    </div>

    <div id="table-container">
        <!-- MAIN DATA TABLE SECTION -->
        <div class="table-wrapper card p-0 overflow-hidden" x-data="{ tableLoading: false }">
        <div class="table-loading-overlay" x-show="tableLoading" style="display: none;">
            <div class="table-spinner"></div>
        </div>

        @if($data->isEmpty())
            <div class="table-empty-state">
                <div class="table-empty-icon">
                    <i class="ti ti-hash text-primary"></i>
                </div>
                <span class="table-empty-title">Tidak ada data angkatan</span>
                <span class="table-empty-sub">Belum ada kelompok angkatan yang terdaftar.</span>
                <div class="table-empty-actions">
                    <button type="button" class="btn btn-primary" @click="showTambah = true">Tambah Angkatan</button>
                </div>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Nama / Tahun Angkatan</th>
                        <th class="col-actions col-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr>
                            <td class="col-no">
                                {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                            </td>
                            <td>
                                <span class="font-bold text-primary-900">{{ $row->name }}</span>
                            </td>
                            <td class="col-actions col-center">
                                <div class="action-buttons-group">
                                    <!-- Edit Record -->
                                    <x-tooltip text="Edit Data">
                                        <button type="button" class="btn btn-ghost action-edit"
                                                @click="showEdit = true; editId = '{{ $row->id }}'; editName = '{{ addslashes($row->name) }}'">
                                            <i class="ti ti-pencil"></i>
                                        </button>
                                    </x-tooltip>
                                    
                                    <!-- Delete Button -->
                                    <x-tooltip text="Hapus Data">
                                        <button type="button" class="btn btn-ghost action-delete" 
                                                @click="$dispatch('confirm-delete', {
                                                    url: '{{ route('angkatan.destroy', $row->id) }}',
                                                    name: 'Angkatan {{ addslashes($row->name) }}'
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

        <!-- PAGINATION -->
        @if(!$data->isEmpty())
            <div class="mt-4" id="pagination-container">
                {{ $data->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>

    <!-- Reusable Hapus Modal -->
    <x-modal-hapus />

    <!-- MODAL TAMBAH INLINE -->
    <div class="modal-backdrop-blur" x-show="showTambah" x-transition style="display: none; position: fixed; inset: 0; z-index: 100; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
        <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px;">
            <div class="card shadow-lg" style="width: 100%; max-width: 500px; padding: 0; overflow: hidden; border-radius: var(--radius-lg);" @click.away="showTambah = false">
                <div class="card-header d-flex align-center justify-between" style="background-color: var(--color-primary-900); color: white; padding: 16px 24px;">
                    <h3 class="card-title text-white" style="margin: 0; font-size: 16px; font-weight: 700;">Tambah Angkatan</h3>
                    <button type="button" class="btn btn-ghost text-white p-1" style="min-width: unset; height: unset; color: white !important;" @click="showTambah = false">
                        <i class="ti ti-x" style="font-size: 20px;"></i>
                    </button>
                </div>
                <form action="{{ route('angkatan.store') }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
                    @csrf
                    <div style="padding: 24px;">
                        <div class="form-group mb-0">
                            <label for="name" class="form-label">Nama / Tahun Angkatan <span class="required-indicator">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Contoh: Angkatan 5 atau 2024" required>
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
    </div>

    <!-- MODAL EDIT INLINE -->
    <div class="modal-backdrop-blur" x-show="showEdit" x-transition style="display: none; position: fixed; inset: 0; z-index: 100; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
        <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px;">
            <div class="card shadow-lg" style="width: 100%; max-width: 500px; padding: 0; overflow: hidden; border-radius: var(--radius-lg);" @click.away="showEdit = false">
                <div class="card-header d-flex align-center justify-between" style="background-color: var(--color-primary-900); color: white; padding: 16px 24px;">
                    <h3 class="card-title text-white" style="margin: 0; font-size: 16px; font-weight: 700;">Edit Angkatan</h3>
                    <button type="button" class="btn btn-ghost text-white p-1" style="min-width: unset; height: unset; color: white !important;" @click="showEdit = false">
                        <i class="ti ti-x" style="font-size: 20px;"></i>
                    </button>
                </div>
                <form :action="'/admin/angkatan/' + editId" method="POST" x-data="{ loading: false }" @submit="loading = true">
                    @csrf
                    @method('PUT')
                    <div style="padding: 24px;">
                        <div class="form-group mb-0">
                            <label for="edit_name" class="form-label">Nama / Tahun Angkatan <span class="required-indicator">*</span></label>
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

</div>
@endsection
