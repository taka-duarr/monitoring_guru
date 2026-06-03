@extends('layouts.admin')

@section('title', 'Manajemen Akun - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
@endpush

@section('content')
<div class="position-relative" x-data="{ tableLoading: false }">
    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Manajemen Akun</h2>
            <p class="text-sm text-neutral-500">Kelola kredensial login, hak akses, dan status keaktifan seluruh pengguna sistem</p>
        </div>
        <!-- Tambah Akun Button -->
        <a href="{{ route('users.create') }}" class="btn btn-primary d-flex align-center gap-2">
            <i class="ti ti-plus"></i> Tambah Akun
        </a>
    </div>

    <!-- Alert Success / Error -->
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

    <!-- FILTER & CONTROL SECTION -->
    <div class="table-filter-wrapper">
        <form id="filterForm" action="{{ route('users.index') }}" method="GET" @submit="tableLoading = true" class="filter-controls-row">
            <!-- 1. Search Bar -->
            <div class="search-input-wrapper">
                <i class="ti ti-search search-icon-inside"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau NIK..." aria-label="Cari Akun">
            </div>

            <!-- 2. Dropdown Filter: Jabatan (Role) -->
            <select name="jabatan" class="filter-select" style="color: #111827; background: #ffffff;" @change="tableLoading = true; $el.form.submit()">
                <option value="">Semua Jabatan</option>
                <option value="admin" {{ $jabatan === 'admin' ? 'selected' : '' }}>Admin / Pengelola</option>
                <option value="guru" {{ $jabatan === 'guru' ? 'selected' : '' }}>Guru / Pengajar</option>
                <option value="ketuakelas" {{ $jabatan === 'ketuakelas' ? 'selected' : '' }}>Ketua Kelas</option>
            </select>

            <!-- 3. Dropdown Filter: Status -->
            <select name="status" class="filter-select" style="color: #111827; background: #ffffff;" @change="tableLoading = true; $el.form.submit()">
                <option value="">Semua Status</option>
                <option value="Aktif" {{ $status === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Cuti" {{ $status === 'Cuti' ? 'selected' : '' }}>Cuti</option>
                <option value="Pensiun" {{ $status === 'Pensiun' ? 'selected' : '' }}>Pensiun</option>
            </select>

            <!-- 4. Reset Button -->
            @if($activeFilterCount > 0)
                <div class="active-filters-info">
                    <span class="badge badge-info">{{ $activeFilterCount }} filter aktif</span>
                    <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm text-primary" @click="tableLoading = true">
                        <i class="ti ti-rotate"></i> Reset
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- MAIN DATA TABLE SECTION -->
    <div class="table-wrapper card p-0 overflow-hidden">
        <div class="table-loading-overlay" x-show="tableLoading" style="display: none;">
            <div class="table-spinner"></div>
        </div>

        @if($data->isEmpty())
            <div class="table-empty-state">
                <div class="table-empty-icon">
                    <i class="ti ti-user-off"></i>
                </div>
                <span class="table-empty-title">Tidak ada data akun</span>
                <span class="table-empty-sub">Belum ada akun yang terdaftar atau tidak sesuai dengan filter pencarian Anda.</span>
                <div class="table-empty-actions">
                    <a href="{{ route('users.create') }}" class="btn btn-primary">Tambah Akun Baru</a>
                </div>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Nama Pengguna</th>
                        <th>NIK / Username</th>
                        <th>Jenis Kelamin</th>
                        <th>No. Telepon</th>
                        <th>Jabatan (Role)</th>
                        <th>Detail Khusus</th>
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
                                <code class="text-xs bg-neutral-100 text-neutral-800 px-2 py-1 rounded">{{ $row->nik }}</code>
                            </td>
                            <td>
                                {{ $row->jenis_kelamin === 'L' ? 'Laki-laki' : ($row->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}
                            </td>
                            <td>
                                {{ $row->no_telp ?: '-' }}
                            </td>
                            <td>
                                @if($row->jabatan === 'admin')
                                    <span class="badge badge-info capitalize">Admin</span>
                                @elseif($row->jabatan === 'guru')
                                    <span class="badge badge-success capitalize">Guru</span>
                                @elseif($row->jabatan === 'ketuakelas')
                                    <span class="badge badge-warning capitalize">Ketua Kelas</span>
                                @else
                                    <span class="badge badge-secondary capitalize">{{ $row->jabatan }}</span>
                                @endif
                            </td>
                            <td>
                                @if($row->jabatan === 'ketuakelas')
                                    @if($row->kelas)
                                        <span class="class-pill-item">Kelas {{ $row->kelas->name }}</span>
                                    @else
                                        <span class="text-neutral-400 text-xs">-</span>
                                    @endif
                                @else
                                    <span class="text-neutral-400 text-xs">Semua Hak Akses</span>
                                @endif
                            </td>
                            <td>
                                @if($row->status === 'Aktif')
                                    <span class="badge badge-success">Aktif</span>
                                @elseif($row->status === 'Cuti')
                                    <span class="badge badge-warning">Cuti</span>
                                @elseif($row->status === 'Pensiun')
                                    <span class="badge badge-danger">Pensiun</span>
                                @else
                                    <span class="badge badge-secondary">{{ $row->status }}</span>
                                @endif
                            </td>
                            <td class="col-actions col-center">
                                <div class="action-buttons-group">
                                    <!-- Edit Record -->
                                    <x-tooltip text="Edit Akun">
                                        <a href="{{ route('users.edit', $row->id) }}" class="btn btn-ghost action-edit">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                    </x-tooltip>

                                    <!-- Delete Button (Hanya jika bukan diri sendiri) -->
                                    @if($row->id !== auth()->id())
                                        <x-tooltip text="Hapus Akun">
                                            <button type="button" class="btn btn-ghost action-delete"
                                                    @click="$dispatch('confirm-delete', {
                                                        url: '{{ route('users.destroy', $row->id) }}',
                                                        name: 'Akun {{ addslashes($row->name) }}'
                                                    })">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </x-tooltip>
                                    @else
                                        <x-tooltip text="Diri Sendiri">
                                            <button type="button" class="btn btn-ghost text-neutral-300" style="cursor: not-allowed;" disabled>
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </x-tooltip>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="table-pagination-wrapper">
                {{ $data->links() }}
            </div>
        @endif
    </div>

    <!-- Include modal-hapus component -->
    <x-modal-hapus />
</div>
@endsection
