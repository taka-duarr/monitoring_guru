@extends('layouts.admin')

@section('title', 'Mata Pelajaran - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
@endpush

@section('content')
<div class="position-relative">
    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Mata Pelajaran</h2>
            <p class="text-sm text-neutral-500">Kelola kurikulum dan daftar mata pelajaran yang diajarkan</p>
        </div>
        <a href="{{ route('mapel.create') }}" class="btn btn-primary d-flex align-center gap-2">
            <i class="ti ti-plus"></i> Tambah Mapel
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
                    <i class="ti ti-book"></i>
                </div>
                <span class="table-empty-title">Tidak ada data mata pelajaran</span>
                <span class="table-empty-sub">Belum ada mata pelajaran yang terdaftar.</span>
                <div class="table-empty-actions">
                    <a href="{{ route('mapel.create') }}" class="btn btn-primary">Tambah Mapel Baru</a>
                </div>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Kategori Jurusan</th>
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
                                <span class="font-semibold text-neutral-800">{{ $row->name }}</span>
                            </td>
                            <td>
                                @if($row->jurusan)
                                    <span class="class-pill-item">{{ $row->jurusan->name }}</span>
                                @else
                                    <span class="text-neutral-400 text-sm">Umum / Semua Jurusan</span>
                                @endif
                            </td>
                            <td class="col-actions col-center">
                                <div class="action-buttons-group">
                                    <!-- Edit Record -->
                                    <x-tooltip text="Edit Data">
                                        <a href="{{ route('mapel.edit', $row->id) }}" class="btn btn-ghost action-edit">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                    </x-tooltip>
                                    
                                    <!-- Delete Button -->
                                    <x-tooltip text="Hapus Data">
                                        <button type="button" class="btn btn-ghost action-delete" 
                                                @click="$dispatch('confirm-delete', {
                                                    url: '{{ route('mapel.destroy', $row->id) }}',
                                                    name: '{{ addslashes($row->name) }}'
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