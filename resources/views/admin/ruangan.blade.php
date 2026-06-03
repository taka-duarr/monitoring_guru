@extends('layouts.admin')

@section('title', 'Manajemen Ruangan - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
@endpush

@section('content')
<div class="position-relative">
    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Data Ruangan</h2>
            <p class="text-sm text-neutral-500">Kelola daftar ruangan belajar, kelas, laboratorium, dan aula sekolah</p>
        </div>
        <a href="{{ route('ruangan.create') }}" class="btn btn-primary d-flex align-center gap-2">
            <i class="ti ti-plus"></i> Tambah Ruangan
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
                    <i class="ti ti-door text-primary"></i>
                </div>
                <span class="table-empty-title">Tidak ada data ruangan</span>
                <span class="table-empty-sub">Belum ada ruangan yang terdaftar.</span>
                <div class="table-empty-actions">
                    <a href="{{ route('ruangan.create') }}" class="btn btn-primary">Tambah Ruangan Baru</a>
                </div>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Nama Ruangan</th>
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
                            <td class="col-actions col-center">
                                <div class="action-buttons-group">
                                    <!-- Edit Record -->
                                    <x-tooltip text="Edit Data">
                                        <a href="{{ route('ruangan.edit', $row->id) }}" class="btn btn-ghost action-edit">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                    </x-tooltip>
                                    
                                    <!-- Delete Button -->
                                    <x-tooltip text="Hapus Data">
                                        <button type="button" class="btn btn-ghost action-delete" 
                                                @click="$dispatch('confirm-delete', {
                                                    url: '{{ route('ruangan.destroy', $row->id) }}',
                                                    name: 'Ruangan {{ addslashes($row->name) }}'
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