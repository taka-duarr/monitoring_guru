@extends('layouts.admin')

@section('title', 'Rekap Kehadiran Masuk - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
@endpush

@section('content')
<div class="position-relative">
    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Kehadiran Masuk</h2>
            <p class="text-sm text-neutral-500">Pantau dan kelola seluruh log kehadiran masuk guru pengampu kelas</p>
        </div>
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
                <span class="table-empty-title">Tidak ada data kehadiran masuk</span>
                <span class="table-empty-sub">Belum ada log kehadiran masuk guru yang tercatat.</span>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Tanggal</th>
                        <th>Guru</th>
                        <th>Kelas / Mapel</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
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
                                {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}
                            </td>
                            <td>
                                <span class="font-semibold text-neutral-800">{{ $row->guru->name ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="font-medium text-neutral-800">{{ $row->kelas->name ?? '-' }}</div>
                                <div class="text-xs text-neutral-500">{{ $row->jadwalAjar->mapel->name ?? '-' }}</div>
                            </td>
                            <td class="font-bold text-success-600">{{ $row->jam_masuk }}</td>
                            <td class="font-bold {{ $row->absenKeluar ? 'text-danger-500' : 'text-neutral-300' }}">
                                {{ $row->absenKeluar->jam_keluar ?? '--:--:--' }}
                            </td>
                            <td class="col-center">
                                @if($row->absenKeluar)
                                    <span class="badge badge-success">Selesai</span>
                                @else
                                    <span class="badge badge-warning">Belum Keluar</span>
                                @endif
                            </td>
                            <td class="col-actions col-center">
                                <div class="action-buttons-group">
                                    <!-- Lihat Murid -->
                                    <x-tooltip text="Lihat Murid">
                                        <a href="{{ route('absenmasuk.murid', $row->id) }}" class="btn btn-ghost action-view">
                                            <i class="ti ti-users"></i>
                                        </a>
                                    </x-tooltip>
                                    
                                    <!-- Edit Record -->
                                    <x-tooltip text="Edit Data">
                                        <a href="{{ route('absenmasuk.edit', $row->id) }}" class="btn btn-ghost action-edit">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                    </x-tooltip>
                                    
                                    <!-- Delete Button -->
                                    <x-tooltip text="Hapus Data">
                                        <button type="button" class="btn btn-ghost action-delete" 
                                                @click="$dispatch('confirm-delete', {
                                                    url: '{{ route('absenmasuk.destroy', $row->id) }}',
                                                    name: 'Kehadiran {{ addslashes($row->guru->name ?? 'Guru') }}'
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