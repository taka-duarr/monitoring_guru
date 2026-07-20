@extends('layouts.admin')

@section('title', 'Manajemen Data Guru - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
@endpush

@section('content')
<!-- Page Container for Alpine-based States (Filter Loading & Modals) -->
<div x-data="{ 
    tableLoading: false, 
    exportDropdownOpen: false,
    showImportModal: false
}" class="position-relative">

    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Manajemen Guru</h2>
            <p class="text-sm text-neutral-500">Kelola dan pantau seluruh data guru serta staff pengampu kelas</p>
        </div>
        <!-- Actions Button Group -->
        <div class="d-flex align-center gap-2">
            <!-- Import Excel Button -->
            <button type="button" @click="showImportModal = true" class="btn btn-secondary d-flex align-center gap-2">
                <i class="ti ti-file-upload"></i> Import Excel
            </button>
            <!-- Tambah Guru Button (Top Right) -->
            <a href="{{ route('guru.create') }}" class="btn btn-primary d-flex align-center gap-2">
                <i class="ti ti-plus"></i> Tambah Guru
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

    <!-- FILTER & CONTROL SECTION -->
    <div class="table-filter-wrapper">
        <form id="filterForm" action="{{ route('guru.index') }}" method="GET" @submit="tableLoading = true" class="filter-controls-row">
            <!-- Maintain sorting state -->
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="dir" value="{{ $dir }}">
            <input type="hidden" name="per_page" value="{{ $perPage }}">

            <!-- 1. Search Bar with Loupe Icon -->
            <div style="position: relative;">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control live-search-input pl-10" placeholder="Cari guru..." style="padding-left: 2.5rem; width: 250px;">
                <i class="ti ti-search text-neutral-400" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%);"></i>
            </div>

            <!-- 2. Dropdown Filter: Status -->
            <select name="status" class="filter-select live-filter-input" @change="tableLoading = true; $el.form.submit()">
                <option value="">Semua Status</option>
                <option value="Aktif" {{ (isset($filters['status']) && $filters['status'] == 'Aktif') ? 'selected' : '' }}>Aktif</option>
                <option value="Cuti" {{ (isset($filters['status']) && $filters['status'] == 'Cuti') ? 'selected' : '' }}>Cuti</option>
                <option value="Pensiun" {{ (isset($filters['status']) && $filters['status'] == 'Pensiun') ? 'selected' : '' }}>Pensiun</option>
            </select>

            <!-- 5. Reset Filter Button & Counter -->
            @if($activeFilterCount > 0)
                <div class="active-filters-info" id="active-filter-count">
                    <span class="badge badge-info">{{ $activeFilterCount }} filter aktif</span>
                    <a href="{{ route('guru.index') }}" class="btn btn-ghost btn-sm text-primary" @click="tableLoading = true">
                        <i class="ti ti-rotate"></i> Reset
                    </a>
                </div>
            @endif

            <!-- 6. Export Button Dropdown (Right side) -->
            <div class="filter-actions-right position-relative" @click.away="exportDropdownOpen = false">
                <button type="button" class="btn btn-secondary d-flex align-center gap-2" @click="exportDropdownOpen = !exportDropdownOpen">
                    <i class="ti ti-download"></i> Ekspor <i class="ti ti-chevron-down" style="font-size: 12px;"></i>
                </button>
                
                <!-- Dropdown export formats -->
                <div class="card shadow-md p-1" 
                     x-show="exportDropdownOpen" 
                     x-transition 
                     style="position: absolute; right: 0; top: 45px; width: 150px; z-index: 80; display: none;">
                    <a href="{{ route('guru.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" 
                       class="d-flex align-center gap-2 px-2 py-2 text-sm text-neutral-700 hover:bg-neutral-50 rounded"
                       style="text-decoration: none;">
                        <i class="ti ti-file-text text-danger" style="font-size: 16px;"></i> Ekspor PDF
                    </a>
                    <a href="{{ route('guru.export', array_merge(request()->query(), ['format' => 'excel'])) }}" 
                       class="d-flex align-center gap-2 px-2 py-2 text-sm text-neutral-700 hover:bg-neutral-50 rounded"
                       style="text-decoration: none;">
                        <i class="ti ti-file-spreadsheet text-success" style="font-size: 16px;"></i> Ekspor Excel
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- METADATA & PER-PAGE AREA -->
    <div class="table-meta-bar">
        <div class="table-meta-left">
            Menampilkan <strong>{{ $data->firstItem() ?? 0 }}–{{ $data->lastItem() ?? 0 }}</strong> dari <strong>{{ $data->total() }}</strong> data guru
        </div>
        
        <div class="table-meta-right">
            Pilih per halaman:
            <select name="per_page_select" class="filter-select" style="height: 30px; padding: 4px 8px; min-width: 70px;" 
                    @change="tableLoading = true; window.location.href = '{{ request()->fullUrlWithQuery(['per_page' => '']) }}' + $el.value">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
            </select>
        </div>
    </div>

    <div id="table-container">
        <!-- 3. DATA TABLE WRAPPER -->
        <div class="table-wrapper card p-0 overflow-hidden position-relative">
            
            <!-- Loading Overlay -->
            <div class="table-loading-overlay" x-show="tableLoading" style="display: none;">
                <div class="table-spinner"></div>
            </div>

        @if($data->isEmpty())
            <!-- Table Empty State -->
            <div class="table-empty-state">
                <div class="table-empty-icon">
                    <i class="ti ti-users"></i>
                </div>
                <span class="table-empty-title">Tidak ada data guru ditemukan</span>
                <span class="table-empty-sub">Coba ubah filter pencarian Anda atau tambahkan data guru baru.</span>
                <div class="table-empty-actions">
                    @if($activeFilterCount > 0)
                        <a href="{{ route('guru.index') }}" class="btn btn-secondary">Reset Filter</a>
                    @endif
                    <a href="{{ route('guru.create') }}" class="btn btn-primary">Tambah Guru Baru</a>
                </div>
            </div>
        @else
            <!-- Data Grid Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <!-- No Column -->
                        <th class="col-no">No</th>
                        
                        <!-- Foto & Nama Column (Sortable) -->
                        @php
                            $sortNameDir = ($sort === 'name' && $dir === 'asc') ? 'desc' : 'asc';
                            $sortNikDir = ($sort === 'nik' && $dir === 'asc') ? 'desc' : 'asc';
                            $sortStatusDir = ($sort === 'status' && $dir === 'asc') ? 'desc' : 'asc';
                            $sortJabatanDir = ($sort === 'jabatan' && $dir === 'asc') ? 'desc' : 'asc';
                        @endphp
                        <th class="sortable col-avatar-name {{ $sort === 'name' ? 'active-sort' : '' }}" 
                            @click="tableLoading = true; window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'name', 'dir' => $sortNameDir]) }}'">
                            Foto & Nama Lengkap
                            <i class="ti {{ $sort === 'name' ? ($dir === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }} sort-direction-icon"></i>
                        </th>
                        
                        <!-- NIP / NIK Column (Sortable) -->
                        <th class="sortable col-nip {{ $sort === 'nik' ? 'active-sort' : '' }}"
                            @click="tableLoading = true; window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'nik', 'dir' => $sortNikDir]) }}'">
                            NIK / NIP
                            <i class="ti {{ $sort === 'nik' ? ($dir === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }} sort-direction-icon"></i>
                        </th>
                        

                        
                        <!-- Status Column (Sortable) -->
                        <th class="sortable col-status {{ $sort === 'status' ? 'active-sort' : '' }} col-center"
                            @click="tableLoading = true; window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'status', 'dir' => $sortStatusDir]) }}'">
                            Status
                            <i class="ti {{ $sort === 'status' ? ($dir === 'asc' ? 'ti-chevron-up' : 'ti-chevron-down') : 'ti-selector' }} sort-direction-icon"></i>
                        </th>
                        
                        <!-- Aksi Column -->
                        <th class="col-actions col-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $index => $guru)
                        <tr>
                            <!-- No Column -->
                            <td class="col-no">
                                {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                            </td>
                            
                            <!-- Foto & Nama Profile Cell -->
                            <td class="col-avatar-name">
                                <div class="teacher-profile-cell">
                                    @if($guru->foto)
                                        <div class="avatar avatar-md">
                                            <img src="{{ asset('storage/' . $guru->foto) }}" alt="{{ $guru->name }}">
                                        </div>
                                    @else
                                        @php
                                            $initials = collect(explode(' ', $guru->name))->map(fn($n) => substr($n, 0, 1))->take(2)->join('');
                                            $colorIndex = (ord(substr($guru->name, 0, 1)) % 9) + 1;
                                        @endphp
                                        <div class="avatar avatar-md avatar-bg-{{ $colorIndex }}">
                                            {{ $initials }}
                                        </div>
                                    @endif
                                    
                                    <div class="teacher-profile-info">
                                        <span class="teacher-name">{{ $guru->name }}</span>
                                        <span class="teacher-sub capitalize">{{ str_replace('_', ' ', $guru->jabatan) }}</span>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- NIP Monospace Column -->
                            <td class="col-nip">
                                <div>{{ $guru->nik }}</div>
                                @if($guru->device_id)
                                    <div class="mt-1">
                                        <span class="badge" style="background-color: #E0E7FF; color: #4338CA; font-size: 10px; padding: 2px 6px;">
                                            <i class="ti ti-lock"></i> Terikat Device
                                        </span>
                                    </div>
                                @else
                                    <div class="mt-1">
                                        <span class="badge" style="background-color: #F1F5F9; color: #64748B; font-size: 10px; padding: 2px 6px;">
                                            <i class="ti ti-lock-open"></i> Belum Terikat
                                        </span>
                                    </div>
                                @endif
                            </td>
                            

                            
                            <!-- Status Badge Column -->
                            <td class="col-status col-center">
                                @if(strtolower($guru->status) == 'aktif')
                                    <span class="badge badge-success">Aktif</span>
                                @elseif(strtolower($guru->status) == 'cuti')
                                    <span class="badge badge-warning">Cuti</span>
                                @else
                                    <span class="badge badge-neutral">Pensiun</span>
                                @endif
                            </td>
                            
                            <!-- Aksi Row Action Buttons with hover tooltips -->
                            <td class="col-actions col-center">
                                <div class="action-buttons-group">
                                    <!-- View Detail -->
                                    <x-tooltip text="Detail Guru">
                                        <button type="button" class="btn btn-ghost action-view"
                                                @click="$dispatch('open-detail-modal', {
                                                    guru: {
                                                        id: '{{ $guru->id }}',
                                                        name: '{{ addslashes($guru->name) }}',
                                                        nik: '{{ $guru->nik }}',
                                                        foto: '{{ $guru->foto ? asset('storage/' . $guru->foto) : '' }}',
                                                        status: '{{ $guru->status }}',
                                                        jenis_kelamin: '{{ $guru->jenis_kelamin ?? '-' }}',
                                                        no_telp: '{{ $guru->no_telp ?? '-' }}'
                                                    },
                                                    editUrl: '{{ route('guru.edit', $guru->id) }}'
                                                })">
                                            <i class="ti ti-eye"></i>
                                        </button>
                                    </x-tooltip>
                                    
                                    <!-- Edit Record -->
                                    <x-tooltip text="Edit Data">
                                        <a href="{{ route('guru.edit', $guru->id) }}" class="btn btn-ghost action-edit">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                    </x-tooltip>
                                    
                                    <!-- Reset Device -->
                                    @if($guru->device_id)
                                        <x-tooltip text="Reset Perangkat">
                                            <form action="{{ route('guru.reset-device', $guru->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin mereset kuncian perangkat untuk guru ini?')">
                                                @csrf
                                                <button type="submit" class="btn btn-ghost action-edit text-warning-600">
                                                    <i class="ti ti-device-mobile-x"></i>
                                                </button>
                                            </form>
                                        </x-tooltip>
                                    @endif
                                    
                                    <!-- Delete Button (triggers modal) -->
                                    <x-tooltip text="Hapus Guru">
                                        <button type="button" class="btn btn-ghost action-delete" 
                                                @click="$dispatch('confirm-delete', {
                                                    url: '{{ route('guru.destroy', $guru->id) }}',
                                                    name: '{{ addslashes($guru->name) }}'
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
        
        <!-- PAGINATION LINKS AREA -->
        @if(!$data->isEmpty())
            <div class="mt-4" id="pagination-container">
                {{ $data->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>

    <!-- Hapus Modal Component -->
    <x-modal-hapus />
    <x-modal-detail />

    <!-- MODAL IMPORT GURU -->
    <div class="modal-backdrop" x-cloak
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
                <h3 class="text-lg font-bold text-primary-900">Import Data Guru</h3>
                <button type="button" @click="showImportModal = false" class="btn btn-ghost p-1 text-neutral-400 hover:text-neutral-600">
                    <i class="ti ti-x" style="font-size: 20px;"></i>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('guru.import') }}" method="POST" enctype="multipart/form-data" x-data="{ importing: false }" @submit="importing = true">
                @csrf
                
                <div class="d-flex flex-column gap-4">
                    <p class="text-sm text-neutral-600">
                        Unggah berkas data guru Anda dalam format <strong>Excel (.xlsx)</strong> atau <strong>CSV</strong>. Silakan unduh template di bawah untuk melihat kolom yang wajib diisi.
                    </p>

                    <!-- Template Link -->
                    <a href="{{ route('guru.import.template') }}" class="d-inline-flex align-center gap-2 text-sm text-primary font-semibold hover:underline" style="text-decoration: none;">
                        <i class="ti ti-download"></i> Unduh Template Excel / CSV
                    </a>

                    <!-- File input -->
                    <div class="form-group mt-2">
                        <label for="import_file" class="form-label">Pilih Berkas (.xlsx, .xls, .csv)</label>
                        <input type="file" name="file" id="import_file" class="form-control" accept=".xlsx,.xls,.csv,.txt" required>
                    </div>

                    <div class="mt-2 text-xs text-neutral-400">
                        * Catatan: Data NIP/ID guru maksimal 50 karakter dan harus unik. Nama mapel dan kelas pengampu harus sesuai dengan data master sistem.
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