@extends('layouts.admin')

@section('title', 'Perizinan Guru - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
@endpush

@section('content')
<div class="position-relative">
    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Perizinan Guru</h2>
            <p class="text-sm text-neutral-500">Kelola dan pantau seluruh pengajuan izin tidak mengajar dari guru</p>
        </div>
        <a href="{{ route('izin.create') }}" class="btn btn-primary d-flex align-center gap-2">
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
                    <i class="ti ti-mail-opened"></i>
                </div>
                <span class="table-empty-title">Tidak ada data perizinan</span>
                <span class="table-empty-sub">Belum ada pengajuan izin mengajar yang tercatat.</span>
                <div class="table-empty-actions">
                    <a href="{{ route('izin.create') }}" class="btn btn-primary">Buat Pengajuan Izin</a>
                </div>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Tanggal</th>
                        <th>Jadwal Ajar</th>
                        <th>Judul Izin</th>
                        <th>Bukti Lampiran</th>
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
                                {{ \Carbon\Carbon::parse($row->tanggal_izin)->translatedFormat('d M Y') }}
                            </td>
                            <td>
                                <div class="font-medium text-neutral-800">{{ $row->jadwalAjar->mapel->name ?? '-' }}</div>
                                <div class="text-xs text-neutral-500">Guru: {{ $row->guru->name ?? $row->jadwalAjar->guru->name ?? '-' }} (Kelas: {{ $row->jadwalAjar->kelas->name ?? '-' }})</div>
                            </td>
                            <td>
                                <span class="font-semibold text-neutral-800">{{ $row->judul }}</span>
                            </td>
                            <td>
                                @if($row->file)
                                    <a href="{{ asset('storage/' . $row->file) }}" target="_blank" class="btn btn-secondary btn-sm d-inline-flex align-center gap-1.5 py-1 px-2.5" style="text-decoration: none; font-size: 12px;">
                                        <i class="ti ti-file-text text-danger" style="font-size: 15px;"></i> Lihat File
                                    </a>
                                @else
                                    <span class="text-neutral-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="col-center">
                                @if($row->approval)
                                    <span class="badge badge-success">Disetujui</span>
                                @else
                                    <span class="badge badge-warning">Menunggu</span>
                                @endif
                            </td>
                            <td class="col-actions col-center">
                                <div class="action-buttons-group">
                                    @if(!$row->approval)
                                        <!-- Approve Button -->
                                        <x-tooltip text="Setujui Izin">
                                            <form action="{{ route('izin.approve', $row->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-ghost action-edit text-success" onclick="return confirm('Anda yakin ingin menyetujui izin ini?')">
                                                    <i class="ti ti-check"></i>
                                                </button>
                                            </form>
                                        </x-tooltip>
                                    @endif

                                    <!-- Edit Record -->
                                    <x-tooltip text="Edit Data">
                                        <a href="{{ route('izin.edit', $row->id) }}" class="btn btn-ghost action-edit">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                    </x-tooltip>
                                    
                                    <!-- Delete Button -->
                                    <x-tooltip text="Hapus Data">
                                        <button type="button" class="btn btn-ghost action-delete" 
                                                @click="$dispatch('confirm-delete', {
                                                    url: '{{ route('izin.destroy', $row->id) }}',
                                                    name: 'Izin {{ addslashes($row->judul) }}'
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