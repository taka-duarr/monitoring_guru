@extends('layouts.admin')

@section('title', 'Manajemen Kelas - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
@endpush

@section('content')
<div x-data="{ 
    tableLoading: false, 
    exportDropdownOpen: false,
    showImportModal: false
}" class="position-relative">
    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Data Kelas</h2>
            <p class="text-sm text-neutral-500">Kelola kelompok belajar siswa, tingkatan kelas, angkatan, dan jurusan</p>
        </div>
        <div class="d-flex align-center gap-2">
            <!-- Filter Actions (Export) -->
            <div class="position-relative" @click.away="exportDropdownOpen = false">
                <button type="button" class="btn btn-secondary d-flex align-center gap-2" @click="exportDropdownOpen = !exportDropdownOpen">
                    <i class="ti ti-download"></i> Ekspor <i class="ti ti-chevron-down" style="font-size: 12px;"></i>
                </button>
                
                <div class="card shadow-md p-1" 
                     x-show="exportDropdownOpen" 
                     x-transition 
                     style="position: absolute; right: 0; top: 45px; width: 150px; z-index: 80; display: none;">
                    <a href="{{ route('kelas.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" 
                       class="d-flex align-center gap-2 px-2 py-2 text-sm text-neutral-700 hover:bg-neutral-50 rounded"
                       style="text-decoration: none;">
                        <i class="ti ti-file-text text-danger" style="font-size: 16px;"></i> Ekspor PDF
                    </a>
                    <a href="{{ route('kelas.export', array_merge(request()->query(), ['format' => 'excel'])) }}" 
                       class="d-flex align-center gap-2 px-2 py-2 text-sm text-neutral-700 hover:bg-neutral-50 rounded"
                       style="text-decoration: none;">
                        <i class="ti ti-file-spreadsheet text-success" style="font-size: 16px;"></i> Ekspor Excel
                    </a>
                </div>
            </div>
            <!-- Import Button -->
            <button type="button" @click="showImportModal = true" class="btn btn-secondary d-flex align-center gap-2">
                <i class="ti ti-file-upload"></i> Import Excel
            </button>
            <a href="{{ route('kelas.create') }}" class="btn btn-primary d-flex align-center gap-2">
                <i class="ti ti-plus"></i> Tambah Kelas
            </a>
        </div>
    </div>

    <!-- Session Alerts for Success / Error / Import Validation Errors -->
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
                @foreach(session('import_errors') as $import_error)
                    <li>{{ $import_error }}</li>
                @endforeach
            </ul>
            <div class="mt-2 text-xs text-neutral-500 font-medium">
                Seluruh proses import dibatalkan. Silakan perbaiki berkas Excel Anda lalu coba unggah kembali.
            </div>
        </div>
    @endif

    <!-- MAIN DATA TABLE SECTION -->
    <div class="table-wrapper card p-0 overflow-hidden">
        <div class="table-loading-overlay" x-show="tableLoading" style="display: none;">
            <div class="table-spinner"></div>
        </div>

        @if($data->isEmpty())
            <div class="table-empty-state">
                <div class="table-empty-icon">
                    <i class="ti ti-school"></i>
                </div>
                <span class="table-empty-title">Tidak ada data kelas</span>
                <span class="table-empty-sub">Belum ada kelas yang terdaftar.</span>
                <div class="table-empty-actions">
                    <a href="{{ route('kelas.create') }}" class="btn btn-primary">Tambah Kelas Baru</a>
                </div>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Nama Kelas</th>
                        <th>Tingkat (Grade)</th>
                        <th>Jurusan</th>
                        <th>Angkatan</th>
                        <th>Status</th>
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
                            <td>
                                Kelas {{ $row->grade }}
                            </td>
                            <td>
                                @if($row->jurusan)
                                    <span class="class-pill-item">{{ $row->jurusan->name }}</span>
                                @else
                                    <span class="text-neutral-400 text-xs">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-neutral-50 text-neutral-600 border border-neutral-200">
                                    {{ $row->angkatan ? $row->angkatan->name : '-' }}
                                </span>
                            </td>
                            <td>
                                @if($row->is_active)
                                    <span class="badge bg-success-50 text-success-700 border border-success-200">Aktif</span>
                                @else
                                    <span class="badge bg-danger-50 text-danger-700 border border-danger-200">Nonaktif / Lulus</span>
                                @endif
                            </td>
                            <td class="col-actions col-center">
                                <div class="action-buttons-group">
                                    <!-- Kelola Murid -->
                                    <x-tooltip text="Daftar Siswa">
                                        <a href="{{ route('kelas.murid.index', $row->id) }}" class="btn btn-ghost action-view">
                                            <i class="ti ti-users"></i>
                                        </a>
                                    </x-tooltip>

                                    <!-- Edit Record -->
                                    <x-tooltip text="Edit Data">
                                        <a href="{{ route('kelas.edit', $row->id) }}" class="btn btn-ghost action-edit">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                    </x-tooltip>

                                    <!-- Delete Button -->
                                    <x-tooltip text="Hapus Data">
                                        <button type="button" class="btn btn-ghost action-delete"
                                                @click="$dispatch('confirm-delete', {
                                                    url: '{{ route('kelas.destroy', $row->id) }}',
                                                    name: 'Kelas {{ addslashes($row->name) }}'
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
        <div class="mt-4">
            {{ $data->links('vendor.pagination.custom') }}
        </div>
    @endif

    <!-- Reusable Hapus Modal -->
    <x-modal-hapus />

    <!-- MODAL IMPORT KELAS -->
    <div class="modal-backdrop" 
         x-show="showImportModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 100; display: none;">
        
        <div class="card bg-white shadow-xl" 
             @click.away="showImportModal = false"
             style="width: 100%; max-width: 500px; padding: 24px; border-radius: 12px; margin: 20px;"
             x-show="showImportModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <div class="d-flex align-center justify-between pb-3 border-b border-neutral-100 mb-4">
                <h3 class="text-lg font-bold text-primary-900">Import Data Kelas</h3>
                <button type="button" @click="showImportModal = false" class="btn btn-ghost p-1 text-neutral-400 hover:text-neutral-600">
                    <i class="ti ti-x" style="font-size: 20px;"></i>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('kelas.import') }}" method="POST" enctype="multipart/form-data" x-data="{ importing: false }" @submit="importing = true">
                @csrf
                
                <div class="d-flex flex-column gap-4">
                    <p class="text-sm text-neutral-600">
                        Unggah berkas data kelas Anda dalam format <strong>Excel (.xlsx)</strong> atau <strong>CSV</strong>. Silakan unduh template di bawah untuk melihat kolom yang wajib diisi.
                    </p>

                    <!-- Template Link -->
                    <a href="{{ route('kelas.import.template') }}" class="d-inline-flex align-center gap-2 text-sm text-primary font-semibold hover:underline" style="text-decoration: none;">
                        <i class="ti ti-download"></i> Unduh Template Excel / CSV
                    </a>

                    <!-- File input -->
                    <div class="form-group mt-2">
                        <label for="import_file" class="form-label">Pilih Berkas (.xlsx, .xls, .csv)</label>
                        <input type="file" name="file" id="import_file" class="form-control" accept=".xlsx,.xls,.csv,.txt" required>
                    </div>

                    <div class="mt-2 text-xs text-neutral-400">
                        * Catatan: Nama Kelas harus unik. Nama Jurusan dan Angkatan harus sesuai dengan data master sistem.
                    </div>
                </div>

                <!-- Footer buttons -->
                <div class="d-flex justify-end gap-2 mt-6 pt-3 border-t border-neutral-100">
                    <button type="button" @click="showImportModal = false" class="btn btn-secondary" :disabled="importing">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-center gap-2" :disabled="importing">
                        <template x-if="importing">
                            <span class="table-spinner" style="width: 14px; height: 14px; border-width: 2px; border-color: white; border-top-color: transparent;"></span>
                        </template>
                        <span x-text="importing ? 'Memproses...' : 'Mulai Import'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
