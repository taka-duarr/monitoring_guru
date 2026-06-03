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
        <a href="{{ route('jadwalajar.create') }}" class="btn btn-primary d-flex align-center gap-2">
            <i class="ti ti-plus"></i> Tambah Jadwal
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
                                <span class="class-pill-item">{{ $row->kelas->name ?? '-' }}</span>
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
        <div class="mt-4">
            {{ $data->links('vendor.pagination.custom') }}
        </div>
    @endif

    <!-- Reusable Hapus Modal -->
    <x-modal-hapus />
</div>
@endsection