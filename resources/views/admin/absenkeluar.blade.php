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