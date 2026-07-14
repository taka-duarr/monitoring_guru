@extends('layouts.admin')

@section('title', 'Jadwal Mengajar - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
@endpush

@section('content')
<div class="position-relative">
    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Jadwal Mengajar</h2>
            <p class="text-sm text-neutral-500">Kelola jadwal pelajaran, pembagian guru, kelas, dan ruangan belajar</p>
        </div>
        <div class="d-flex gap-2">
            <!-- Import Excel Button -->
            <button type="button" @click="$dispatch('open-import')" class="btn btn-secondary d-flex align-center gap-2">
                <i class="ti ti-upload"></i> Import Excel
            </button>

            <a href="{{ route('jadwalajar.create') }}" class="btn btn-primary d-flex align-center gap-2">
                <i class="ti ti-plus"></i> Tambah Jadwal
            </a>
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
            <div class="font-semibold mb-1 d-flex align-center gap-2">
                <i class="ti ti-alert-circle text-lg"></i>
                Beberapa baris gagal di-import:
            </div>
            <ul class="list-disc pl-5 mt-2">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FILTER TAHUN AJARAN --}}
    <form method="GET" action="{{ route('jadwalajar.index') }}" class="mb-4 p-4 card d-flex align-center gap-3 flex-wrap" style="padding: 16px !important;">
        <div style="flex:1; min-width:200px;">
            <label class="form-label" style="margin-bottom:4px; font-size:11px; text-transform:uppercase; letter-spacing:0.05em;">Tahun Ajaran</label>
            <select name="tahun_ajaran_id" class="form-select live-filter-input" style="padding: 8px 12px; font-size:13px;">
                <option value="">-- Semua Tahun Ajaran --</option>
                @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                        {{ $ta->name }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div style="flex:1; min-width:200px;">
            <label class="form-label" style="margin-bottom:4px; font-size:11px; text-transform:uppercase; letter-spacing:0.05em;">Pencarian</label>
            <div style="position: relative;">
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="form-control live-search-input pl-10" 
                       placeholder="Cari guru, mapel, kelas..." style="padding: 8px 12px 8px 36px; font-size:13px;">
                <i class="ti ti-search text-neutral-400" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%);"></i>
            </div>
        </div>
        <div class="d-flex gap-2 align-center" style="margin-top: auto;">

            @if(request('tahun_ajaran_id') || request('search'))
                <a href="{{ route('jadwalajar.index') }}" class="btn btn-secondary d-flex align-center gap-2" style="padding: 8px 16px;">
                    <i class="ti ti-x"></i> Reset
                </a>
            @endif
        </div>
        @if($selectedTahunAjaran)
            <div style="width:100%; font-size:12px; color: var(--color-primary-700); background: var(--color-primary-50); border: 1px solid var(--color-primary-200); border-radius:8px; padding: 6px 12px;" class="d-flex align-center gap-2">
                <i class="ti ti-calendar-check"></i>
                Menampilkan jadwal Tahun Ajaran: <strong>{{ $selectedTahunAjaran->name }}</strong>
            </div>
        @endif
    </form>

    <div id="table-container">
        <!-- MAIN DATA TABLE SECTION -->
        <div class="table-wrapper card p-0 overflow-hidden" x-data="{ tableLoading: false }">
        <div class="table-loading-overlay" x-show="tableLoading" style="display: none;">
            <div class="table-spinner"></div>
        </div>

        @if($data->isEmpty())
            <div class="table-empty-state">
                <div class="table-empty-icon">
                    <i class="ti ti-calendar"></i>
                </div>
                <span class="table-empty-title">Tidak ada data jadwal mengajar</span>
                <span class="table-empty-sub">Belum ada jadwal pelajaran yang dibuat.</span>
                <div class="table-empty-actions">
                    <a href="{{ route('jadwalajar.create') }}" class="btn btn-primary">Tambah Jadwal Baru</a>
                </div>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Hari</th>
                        <th>Guru Pengajar</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Ruangan</th>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
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
                                <span class="font-bold text-primary-900">{{ $row->hari }}</span>
                            </td>
                            <td>
                                <span class="font-semibold text-neutral-800">{{ $row->guru->name ?? '-' }}</span>
                            </td>
                            <td>
                                {{ $row->mapel->name ?? '-' }}
                            </td>
                            <td>
                                <span class="class-pill-item">
                                    {{ $row->kelas ? ($row->kelas->grade ? $row->kelas->grade . ' ' : '') . $row->kelas->name : '-' }}
                                </span>
                                @if($row->kelas && $row->kelas->angkatan)
                                    <span class="text-xs text-neutral-500 d-block mt-1">Angkatan {{ $row->kelas->angkatan->name }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $row->ruangan->name ?? '-' }}
                            </td>
                            <td class="font-medium text-neutral-700">{{ $row->jam_mulai }}</td>
                            <td class="font-medium text-neutral-700">{{ $row->jam_selesai }}</td>
                            <td class="col-actions col-center">
                                <div class="action-buttons-group">
                                    <!-- Edit Record -->
                                    <x-tooltip text="Edit Data">
                                        <a href="{{ route('jadwalajar.edit', $row->id) }}" class="btn btn-ghost action-edit">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                    </x-tooltip>
                                    
                                    <!-- Delete Button -->
                                    <x-tooltip text="Hapus Data">
                                        <button type="button" class="btn btn-ghost action-delete" 
                                                @click="$dispatch('confirm-delete', {
                                                    url: '{{ route('jadwalajar.destroy', $row->id) }}',
                                                    name: 'Jadwal {{ addslashes($row->guru->name ?? 'Guru') }} - {{ addslashes($row->mapel->name ?? 'Mapel') }}'
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

    <!-- MODAL IMPORT -->
    <div class="modal-backdrop" 
         x-data="{ showImport: false }"
         x-show="showImport" 
         @open-import.window="showImport = true"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 100; align-items: center; justify-content: center;"
         :style="showImport ? 'display: flex;' : 'display: none;'">
        
        <div class="card bg-white shadow-xl" 
             @click.away="showImport = false"
             style="width: 100%; max-width: 500px; padding: 24px; border-radius: 12px; margin: 20px;"
             x-show="showImport"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <div class="d-flex align-center justify-between pb-3 border-b border-neutral-100 mb-4">
                <h3 class="text-lg font-bold text-primary-900">Import Jadwal Mengajar</h3>
                <button type="button" @click="showImport = false" class="btn btn-ghost p-1 text-neutral-400 hover:text-neutral-600">
                    <i class="ti ti-x" style="font-size: 20px;"></i>
                </button>
            </div>
            
            <form action="{{ route('jadwalajar.import') }}" method="POST" enctype="multipart/form-data" x-data="{ importing: false }" @submit="importing = true">
                @csrf
                <div class="d-flex flex-column gap-4">
                    <p class="text-sm text-neutral-600">
                        Unggah berkas data jadwal mengajar Anda dalam format <strong>Excel (.xlsx)</strong> atau <strong>CSV</strong>. Silakan unduh template di bawah untuk melihat kolom yang wajib diisi.
                    </p>

                    <!-- Template Link -->
                    <a href="{{ route('jadwalajar.import.template') }}" class="d-inline-flex align-center gap-2 text-sm text-primary font-semibold hover:underline" style="text-decoration: none;">
                        <i class="ti ti-download"></i> Unduh Template Excel / CSV
                    </a>
                    
                    <div class="form-group mt-2">
                        <label class="form-label font-semibold text-neutral-700" for="file">Pilih File (.xlsx, .xls, .csv)</label>
                        <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls,.csv" required style="padding: 0.5rem;">
                    </div>
                </div>
                
                <div class="d-flex justify-end gap-2 mt-6 pt-3 border-t border-neutral-100">
                    <button type="button" @click="showImport = false" class="btn btn-secondary" :disabled="importing">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-center gap-2" :disabled="importing">
                        <template x-if="importing">
                            <span class="table-spinner" style="width: 14px; height: 14px; border-width: 2px; border-color: white; border-top-color: transparent;"></span>
                        </template>
                        <span x-text="importing ? 'Memproses...' : 'Proses Import'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection