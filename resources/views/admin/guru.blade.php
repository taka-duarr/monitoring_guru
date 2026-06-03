@extends('layouts.admin')

@section('title', 'Manajemen Data Guru - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
@endpush

@section('content')
<!-- Page Container for Alpine-based States (Filter Loading & Modals) -->
<div x-data="{ 
    tableLoading: false, 
    exportDropdownOpen: false 
}" class="position-relative">

    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Manajemen Guru</h2>
            <p class="text-sm text-neutral-500">Kelola dan pantau seluruh data guru serta staff pengampu kelas</p>
        </div>
        <!-- Tambah Guru Button (Top Right) -->
        <a href="{{ route('guru.create') }}" class="btn btn-primary d-flex align-center gap-2">
            <i class="ti ti-plus"></i> Tambah Guru
        </a>
    </div>

    <!-- FILTER & CONTROL SECTION -->
    <div class="table-filter-wrapper">
        <form id="filterForm" action="{{ route('guru.index') }}" method="GET" @submit="tableLoading = true" class="filter-controls-row">
            <!-- Maintain sorting state -->
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="dir" value="{{ $dir }}">
            <input type="hidden" name="per_page" value="{{ $perPage }}">

            <!-- 1. Search Bar with Loupe Icon -->
            <div class="search-input-wrapper">
                <i class="ti ti-search search-icon-inside"></i>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama atau NIP..." aria-label="Cari Guru">
            </div>

            <!-- 2. Dropdown Filter: Mapel -->
            <select name="mapel" class="filter-select" @change="tableLoading = true; $el.form.submit()">
                <option value="">Semua Mapel</option>
                @foreach($allMapels as $mapel)
                    <option value="{{ $mapel->id }}" {{ (isset($filters['mapel']) && $filters['mapel'] == $mapel->id) ? 'selected' : '' }}>
                        {{ $mapel->name }}
                    </option>
                @endforeach
            </select>

            <!-- 3. Dropdown Filter: Status -->
            <select name="status" class="filter-select" @change="tableLoading = true; $el.form.submit()">
                <option value="">Semua Status</option>
                <option value="Aktif" {{ (isset($filters['status']) && $filters['status'] == 'Aktif') ? 'selected' : '' }}>Aktif</option>
                <option value="Cuti" {{ (isset($filters['status']) && $filters['status'] == 'Cuti') ? 'selected' : '' }}>Cuti</option>
                <option value="Pensiun" {{ (isset($filters['status']) && $filters['status'] == 'Pensiun') ? 'selected' : '' }}>Pensiun</option>
            </select>

            <!-- 4. Dropdown Filter: Kelas -->
            <select name="kelas" class="filter-select" @change="tableLoading = true; $el.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($allKelas as $kelasItem)
                    <option value="{{ $kelasItem->id }}" {{ (isset($filters['kelas']) && $filters['kelas'] == $kelasItem->id) ? 'selected' : '' }}>
                        {{ $kelasItem->name }}
                    </option>
                @endforeach
            </select>

            <!-- 5. Reset Filter Button & Counter -->
            @if($activeFilterCount > 0)
                <div class="active-filters-info">
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

    <!-- MAIN DATA TABLE SECTION -->
    <div class="table-wrapper card p-0 overflow-hidden">
        <!-- White Loading Overlay state -->
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
                        
                        <!-- Mata Pelajaran Column -->
                        <th class="col-mapel">Mata Pelajaran</th>
                        
                        <!-- Kelas Pengampu Column -->
                        <th class="col-kelas">Kelas Pengampu</th>
                        
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
                        @php
                            // Fetch mapped relation data
                            $mapelCollection = $guru->jadwalAjars->pluck('mapel.name')->unique();
                            $kelasCollection = $guru->jadwalAjars->pluck('kelas.name')->unique()->values();
                        @endphp
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
                                {{ $guru->nik }}
                            </td>
                            
                            <!-- Mata Pelajaran and Short Abbreviations Badge -->
                            <td class="col-mapel">
                                <div class="d-flex flex-column gap-1">
                                    @if($mapelCollection->isEmpty())
                                        <span class="text-muted text-xs">-</span>
                                    @else
                                        @foreach($mapelCollection as $mapelName)
                                            <div class="mapel-cell">
                                                <span class="mapel-abbr-badge" title="{{ $mapelName }}">
                                                    {{ $guru->getMapelAbbreviation($mapelName) }}
                                                </span>
                                                <span class="text-sm font-medium text-neutral-800">{{ $mapelName }}</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Kelas Pengampu (max 3 pills, collapsible indicator) -->
                            <td class="col-kelas">
                                @if($kelasCollection->isEmpty())
                                    <span class="text-muted text-xs">-</span>
                                @else
                                    <div class="class-pill-list">
                                        @foreach($kelasCollection->take(3) as $className)
                                            <span class="class-pill-item">{{ $className }}</span>
                                        @endforeach
                                        
                                        @if($kelasCollection->count() > 3)
                                            @php
                                                $remainingCount = $kelasCollection->count() - 3;
                                                $allClassesString = $kelasCollection->join(', ');
                                            @endphp
                                            <x-tooltip text="{{ $allClassesString }}">
                                                <span class="class-pill-more">+{{ $remainingCount }} lagi</span>
                                            </x-tooltip>
                                        @endif
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
                                                        tempat_lahir: '{{ addslashes($guru->tempat_lahir ?? '-') }}',
                                                        tanggal_lahir: '{{ $guru->tanggal_lahir ? \Carbon\Carbon::parse($guru->tanggal_lahir)->translatedFormat('d F Y') : '-' }}',
                                                        no_telp: '{{ $guru->no_telp ?? '-' }}',
                                                        status_kepegawaian: '{{ $guru->status_kepegawaian ?? '-' }}',
                                                        golongan: '{{ $guru->status_kepegawaian == 'PNS' ? $guru->golongan : '-' }}',
                                                        tmt: '{{ $guru->tmt ? \Carbon\Carbon::parse($guru->tmt)->translatedFormat('d F Y') : '-' }}',
                                                        jumlah_jam: '{{ $guru->jumlah_jam ?? 0 }}',
                                                        mapel: '{{ addslashes($mapelCollection->join(', ')) }}',
                                                        kelas: '{{ addslashes($kelasCollection->join(', ')) }}'
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
    </div>

    <!-- PAGINATION LINKS AREA (Using overrides custom layout) -->
    @if(!$data->isEmpty())
        <div class="mt-4">
            {{ $data->links('vendor.pagination.custom') }}
        </div>
    @endif

    <!-- Reusable Modal Components -->
    <x-modal-hapus />
    <x-modal-detail />
</div>
@endsection