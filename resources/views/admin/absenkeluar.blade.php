@extends('layouts.admin')

@section('title', 'Rekap Kehadiran Keluar - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
@endpush

@section('content')
<div class="position-relative">
    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Kehadiran Keluar</h2>
            <p class="text-sm text-neutral-500">Daftar rekap absensi pulang / keluar guru pengampu kelas</p>
        </div>
        <a href="{{ route('absenkeluar.create') }}" class="btn btn-primary d-flex align-center gap-2">
            <i class="ti ti-plus"></i> Tambah Data
        </a>
    </div>

    <!-- FILTER SECTION -->
    <div class="bg-white rounded-2xl border border-slate-100 p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('absenkeluar.index') }}" class="flex flex-wrap gap-3 items-end">
            {{-- Cari Nama Guru --}}
            <div class="flex flex-col">
                <label for="filter_guru" class="text-xs text-slate-500 mb-1 font-medium">Cari Nama Guru</label>
                <input type="text"
                    id="filter_guru"
                    name="guru"
                    value="{{ request('guru') }}"
                    placeholder="Nama guru..."
                    class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none min-w-[180px]">
            </div>

            {{-- Pilih Kelas --}}
            <div class="flex flex-col">
                <label for="filter_kelas" class="text-xs text-slate-500 mb-1 font-medium">Pilih Kelas</label>
                <select id="filter_kelas"
                    name="kelas_id"
                    class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none min-w-[160px]">
                    <option value="">Semua Kelas</option>
                    @foreach($allKelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal Masuk --}}
            <div class="flex flex-col">
                <label for="filter_tanggal" class="text-xs text-slate-500 mb-1 font-medium">Tanggal Masuk</label>
                <input type="date"
                    id="filter_tanggal"
                    name="tanggal"
                    value="{{ request('tanggal') }}"
                    class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-150">
                    Filter
                </button>
                <a href="{{ route('absenkeluar.index') }}"
                    class="px-4 py-2 text-sm font-medium rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors duration-150">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- MAIN DATA TABLE SECTION -->
    <div class="table-wrapper card p-0 overflow-hidden" x-data="{ tableLoading: false }">
        <div class="table-loading-overlay" x-show="tableLoading" style="display: none;">
            <div class="table-spinner"></div>
        </div>

        @if($data->isEmpty())
            <div class="table-empty-state">
                <div class="table-empty-icon">
                    <i class="ti ti-calendar-event"></i>
                </div>
                <span class="table-empty-title">Tidak ada data kehadiran keluar</span>
                <span class="table-empty-sub">Belum ada log kehadiran keluar yang tercatat.</span>
                <div class="table-empty-actions">
                    <a href="{{ route('absenkeluar.create') }}" class="btn btn-primary">Tambah Log Keluar</a>
                </div>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Tgl Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Guru</th>
                        <th>Kelas</th>
                        <th>Mapel</th>
                        <th class="col-center">Status</th>
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
                                {{ $row->absenMasuk->tanggal ?? '-' }}
                            </td>
                            <td class="font-bold text-danger-500">
                                {{ $row->jam_keluar }}
                            </td>
                            <td>
                                <span class="font-semibold text-neutral-800">{{ $row->absenMasuk->guru->name ?? '-' }}</span>
                            </td>
                            <td>
                                {{ $row->absenMasuk->kelas->name ?? '-' }}
                            </td>
                            <td>
                                {{ $row->absenMasuk->jadwalAjar->mapel->name ?? '-' }}
                            </td>
                            <td class="col-center">
                                <span class="badge badge-success">{{ $row->status }}</span>
                            </td>
                            <td class="col-actions col-center">
                                <div class="action-buttons-group">
                                    <!-- Edit Record -->
                                    <x-tooltip text="Edit Data">
                                        <a href="{{ route('absenkeluar.edit', $row->id) }}" class="btn btn-ghost action-edit">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                    </x-tooltip>
                                    
                                    <!-- Delete Button -->
                                    <x-tooltip text="Hapus Data">
                                        <button type="button" class="btn btn-ghost action-delete" 
                                                @click="$dispatch('confirm-delete', {
                                                    url: '{{ route('absenkeluar.destroy', $row->id) }}',
                                                    name: 'Keluar {{ addslashes($row->absenMasuk->guru->name ?? 'Guru') }}'
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
</div>
@endsection