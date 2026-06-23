@extends('layouts.admin')

@section('title', 'Rekap Kehadiran - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
@endpush

@section('content')
<div class="position-relative">
    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Rekap Kehadiran</h2>
            <p class="text-sm text-neutral-500">Pantau dan kelola seluruh log kehadiran (masuk & keluar) guru pengampu kelas</p>
        </div>
    </div>

    <!-- FILTER SECTION -->
    <div class="bg-white rounded-2xl border border-slate-100 p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('absenmasuk.index') }}" class="flex flex-wrap gap-3 items-end">
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
                            {{ $k->grade }} {{ $k->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal --}}
            <div class="flex flex-col">
                <label for="filter_tanggal" class="text-xs text-slate-500 mb-1 font-medium">Tanggal</label>
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
                <a href="{{ route('absenmasuk.index') }}"
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
                <span class="table-empty-title">Tidak ada data kehadiran</span>
                <span class="table-empty-sub">Belum ada log kehadiran guru yang tercatat.</span>
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
                            <td>
                                <div class="text-xs text-neutral-500 mb-1">
                                    Jadwal: {{ $row->jadwalAjar->jam_mulai ? substr($row->jadwalAjar->jam_mulai, 0, 5) : '-' }}
                                </div>
                                <div class="font-bold text-success-600">
                                    Absen: {{ substr($row->jam_masuk, 0, 5) }}
                                </div>
                            </td>
                            <td>
                                <div class="text-xs text-neutral-500 mb-1">
                                    Jadwal: {{ $row->jadwalAjar->jam_selesai ? substr($row->jadwalAjar->jam_selesai, 0, 5) : '-' }}
                                </div>
                                <div class="font-bold {{ $row->absenKeluar ? 'text-danger-500' : 'text-neutral-300' }}">
                                    Absen: {{ $row->absenKeluar ? substr($row->absenKeluar->jam_keluar, 0, 5) : '--:--' }}
                                </div>
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