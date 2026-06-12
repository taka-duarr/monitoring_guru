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
    showImport: false,
    editId: '', 
    editNoAbsen: '', 
    editNis: '', 
    editName: '',
    editStatus: ''
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
            <button type="button" class="btn btn-secondary d-flex align-center gap-2" @click="showImport = true">
                <i class="ti ti-upload"></i> Import Excel
            </button>
            <button type="button" class="btn btn-primary d-flex align-center gap-2" @click="showTambah = true">
                <i class="ti ti-plus"></i> Tambah Murid
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="mb-4 p-4 rounded-lg bg-success-50 border border-success-100 text-success-700 text-sm d-flex align-center gap-2">
            <i class="ti ti-circle-check text-lg"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 rounded-lg bg-danger-50 border border-danger-100 text-danger-700 text-sm d-flex align-center gap-2">
            <i class="ti ti-alert-circle text-lg"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if(session('import_errors'))
        <div class="mb-4 p-4 rounded-lg bg-danger-50 border border-danger-100 text-danger-700 text-sm">
            <div class="font-semibold mb-2 d-flex align-center gap-2">
                <i class="ti ti-alert-triangle text-lg text-danger-600"></i>
                Gagal Mengimpor Berkas! Terdapat kesalahan pada data berikut:
            </div>
            <ul class="list-disc pl-5 max-h-48 overflow-y-auto d-flex flex-column gap-1">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 rounded-lg bg-danger-50 border border-danger-100 text-danger-700 text-sm" x-init="showTambah = true">
            <div class="font-semibold mb-2 d-flex align-center gap-2">
                <i class="ti ti-alert-triangle text-lg text-danger-600"></i>
                Gagal menyimpan data! Periksa isian Anda:
            </div>
            <ul class="list-disc pl-5 max-h-48 overflow-y-auto d-flex flex-column gap-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                        <th>Status</th>
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
                            <td>
                                @if($row->status == 'aktif')
                                    <span class="badge bg-success-50 text-success-700 border border-success-200" style="padding: 4px 8px; border-radius: 12px; font-size: 11px;">Aktif</span>
                                @elseif($row->status == 'lulus')
                                    <span class="badge bg-primary-50 text-primary-700 border border-primary-200" style="padding: 4px 8px; border-radius: 12px; font-size: 11px;">Lulus</span>
                                @elseif($row->status == 'pindah')
                                    <span class="badge bg-warning-50 text-warning-700 border border-warning-200" style="padding: 4px 8px; border-radius: 12px; font-size: 11px;">Pindah</span>
                                @else
                                    <span class="badge bg-danger-50 text-danger-700 border border-danger-200" style="padding: 4px 8px; border-radius: 12px; font-size: 11px;">{{ ucfirst($row->status) }}</span>
                                @endif
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
                                                        editName = '{{ addslashes($row->name) }}';
                                                        editStatus = '{{ $row->status }}'">
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
    <div class="modal-backdrop-blur" x-show="showTambah" x-transition style="display: none; position: fixed; inset: 0; z-index: 100; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
        <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px;">
            <div class="card bg-white shadow-xl" style="width: 100%; max-width: 500px; padding: 24px; border-radius: 12px; margin: 20px;" @click.away="showTambah = false">
                <div class="d-flex align-center justify-between pb-3 border-b border-neutral-100 mb-4" style="border-bottom: 1px solid var(--color-neutral-100);">
                    <h3 class="text-lg font-bold text-primary-900" style="margin: 0;">Tambah Murid Baru</h3>
                    <button type="button" class="btn btn-ghost p-1 text-neutral-400 hover:text-neutral-600" style="min-width: unset; height: unset;" @click="showTambah = false">
                        <i class="ti ti-x" style="font-size: 20px;"></i>
                    </button>
                </div>
                <form action="{{ route('kelas.murid.store', $kelas->id) }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
                    @csrf
                    <div class="d-flex flex-column gap-4">
                        <!-- No Absen -->
                        <div class="form-group mb-0">
                            <label for="no_absen" class="form-label">No. Absen</label>
                            <input type="number" id="no_absen" name="no_absen" class="form-control" placeholder="Contoh: 1, 2, 15...">
                        </div>

                        <!-- NIS -->
                        <div class="form-group mb-0">
                            <label for="nis" class="form-label">NIS (Nomor Induk Siswa) <span class="required-indicator">*</span></label>
                            <input type="text" id="nis" name="nis" class="form-control" placeholder="Masukkan NIS siswa..." required>
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="form-group mb-0">
                            <label for="name" class="form-label">Nama Lengkap <span class="required-indicator">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan nama lengkap siswa..." required>
                        </div>
                    </div>
                    <div class="d-flex justify-end gap-2 mt-6 pt-3" style="border-top: 1px solid var(--color-neutral-100); margin-top: 24px; padding-top: 16px;">
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
            <div class="card bg-white shadow-xl" style="width: 100%; max-width: 500px; padding: 24px; border-radius: 12px; margin: 20px;" @click.away="showEdit = false">
                <div class="d-flex align-center justify-between pb-3 border-b border-neutral-100 mb-4" style="border-bottom: 1px solid var(--color-neutral-100);">
                    <h3 class="text-lg font-bold text-primary-900" style="margin: 0;">Edit Data Murid</h3>
                    <button type="button" class="btn btn-ghost p-1 text-neutral-400 hover:text-neutral-600" style="min-width: unset; height: unset;" @click="showEdit = false">
                        <i class="ti ti-x" style="font-size: 20px;"></i>
                    </button>
                </div>
                <form :action="'/admin/kelas/{{ $kelas->id }}/murid/' + editId" method="POST" x-data="{ loading: false }" @submit="loading = true">
                    @csrf
                    @method('PUT')
                    <div class="d-flex flex-column gap-4">
                        <!-- No Absen -->
                        <div class="form-group mb-0">
                            <label for="edit_no_absen" class="form-label">No. Absen</label>
                            <input type="number" id="edit_no_absen" name="no_absen" class="form-control" x-model="editNoAbsen">
                        </div>

                        <!-- NIS -->
                        <div class="form-group mb-0">
                            <label for="edit_nis" class="form-label">NIS (Nomor Induk Siswa) <span class="required-indicator">*</span></label>
                            <input type="text" id="edit_nis" name="nis" class="form-control" x-model="editNis" required>
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="form-group mb-0">
                            <label for="edit_name" class="form-label">Nama Lengkap <span class="required-indicator">*</span></label>
                            <input type="text" id="edit_name" name="name" class="form-control" x-model="editName" required>
                        </div>

                        <!-- Status -->
                        <div class="form-group mb-0">
                            <label for="edit_status" class="form-label">Status <span class="required-indicator">*</span></label>
                            <select id="edit_status" name="status" class="form-select" x-model="editStatus" required>
                                <option value="aktif">Aktif</option>
                                <option value="lulus">Lulus</option>
                                <option value="pindah">Pindah</option>
                                <option value="keluar">Keluar / Drop Out</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-end gap-2 mt-6 pt-3" style="border-top: 1px solid var(--color-neutral-100); margin-top: 24px; padding-top: 16px;">
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

    <!-- MODAL IMPORT -->
    <div class="modal-backdrop-blur" x-show="showImport" x-transition style="display: none; position: fixed; inset: 0; z-index: 100; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
        <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px;">
            <div class="card bg-white shadow-xl" style="width: 100%; max-width: 500px; padding: 24px; border-radius: 12px; margin: 20px;" @click.away="showImport = false">
                <div class="d-flex align-center justify-between pb-3 border-b border-neutral-100 mb-4" style="border-bottom: 1px solid var(--color-neutral-100);">
                    <h3 class="text-lg font-bold text-primary-900" style="margin: 0;">Import Data Murid</h3>
                    <button type="button" class="btn btn-ghost p-1 text-neutral-400 hover:text-neutral-600" style="min-width: unset; height: unset;" @click="showImport = false">
                        <i class="ti ti-x" style="font-size: 20px;"></i>
                    </button>
                </div>
                
                <form action="{{ route('kelas.murid.import', $kelas->id) }}" method="POST" enctype="multipart/form-data" x-data="{ loading: false }" @submit="loading = true">
                    @csrf
                    
                    <div class="d-flex flex-column gap-4">
                        <p class="text-sm text-neutral-600" style="margin-bottom: 16px;">
                            Unggah berkas data murid Anda dalam format <strong>Excel (.xlsx)</strong> atau <strong>CSV</strong>. Silakan unduh template di bawah untuk melihat format kolom.
                        </p>

                        <!-- Template Link -->
                        <a href="{{ route('kelas.murid.import.template', $kelas->id) }}" class="d-inline-flex align-center gap-2 text-sm text-primary font-semibold" style="text-decoration: none; color: var(--color-primary-600); margin-bottom: 16px;">
                            <i class="ti ti-download"></i> Unduh Template Excel / CSV
                        </a>

                        <!-- File input -->
                        <div class="form-group mb-0">
                            <label for="import_file" class="form-label">Pilih Berkas (.xlsx, .xls, .csv) <span class="required-indicator">*</span></label>
                            <input type="file" name="file" id="import_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        </div>
                        
                        <div class="mt-2 text-xs text-neutral-400" style="margin-top: 8px;">
                            * Pastikan penamaan kolom pada baris pertama persis sama dengan yang ada di template.
                        </div>
                    </div>
                    
                    <div class="d-flex justify-end gap-2 mt-6 pt-3" style="border-top: 1px solid var(--color-neutral-100); margin-top: 24px; padding-top: 16px;">
                        <button type="button" class="btn btn-secondary" @click="showImport = false" :disabled="loading">Batal</button>
                        <button type="submit" class="btn btn-primary d-flex align-center gap-2" :disabled="loading">
                            <template x-if="loading">
                                <span class="table-spinner" style="width: 14px; height: 14px; border-width: 2px; border-color: white; border-top-color: transparent;"></span>
                            </template>
                            <span x-text="loading ? 'Memproses...' : 'Mulai Import'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
