@extends('layouts.admin')

@section('title', 'Riwayat Laporan - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
<style>
    .format-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.04em;
    }
    .format-badge-pdf   { background: #fee2e2; color: #991b1b; }
    .format-badge-excel { background: #d1fae5; color: #065f46; }

    .jenis-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.72rem; font-weight: 600;
        background: #eef2fb; color: #1B2A4A;
    }

    .action-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 34px; height: 34px;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.15s;
        text-decoration: none;
        border: none; cursor: pointer;
    }
    .action-btn-download {
        background: #eef2fb; color: #1B2A4A;
    }
    .action-btn-download:hover { background: #1B2A4A; color: white; }
    .action-btn-delete {
        background: #fee2e2; color: #991b1b;
    }
    .action-btn-delete:hover { background: #991b1b; color: white; }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--color-neutral-400, #9ca3af);
    }
    .empty-state i { font-size: 52px; display: block; margin-bottom: 12px; }
    .empty-state p { font-size: 0.9rem; margin-bottom: 16px; }
</style>
@endpush

@section('content')
<div x-data="{
    deleteUrl: '',
    deleteName: '',
    showDeleteModal: false
}">

    {{-- Header --}}
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Riwayat Laporan</h2>
            <p class="text-sm text-neutral-500">Daftar semua laporan yang pernah digenerate — dapat diunduh kembali kapan saja</p>
        </div>
        <a href="{{ route('laporan.index') }}" class="btn btn-primary d-flex align-center gap-2">
            <i class="ti ti-plus"></i> Buat Laporan Baru
        </a>
    </div>

    @if(session('success'))
    <div class="alert mb-4" style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:10px;color:#065f46;">
        <i class="ti ti-circle-check" style="font-size:18px;"></i>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert mb-4" style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:10px;color:#991b1b;">
        <i class="ti ti-alert-circle" style="font-size:18px;"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- Filter Bar --}}
    <div class="table-filter-wrapper">
        <form action="{{ route('laporan.riwayat') }}" method="GET" class="filter-controls-row">
            <select name="jenis" class="filter-select" onchange="this.form.submit()">
                <option value="">Semua Jenis</option>
                <option value="rekap_kehadiran" {{ request('jenis') === 'rekap_kehadiran' ? 'selected' : '' }}>Rekap Kehadiran</option>
                <option value="perizinan"       {{ request('jenis') === 'perizinan'       ? 'selected' : '' }}>Perizinan</option>
                <option value="kelas_kosong"    {{ request('jenis') === 'kelas_kosong'    ? 'selected' : '' }}>Kelas Kosong</option>
                <option value="jadwal_ajar"     {{ request('jenis') === 'jadwal_ajar'     ? 'selected' : '' }}>Jadwal Mengajar</option>
            </select>
            <select name="format" class="filter-select" onchange="this.form.submit()">
                <option value="">Semua Format</option>
                <option value="pdf"   {{ request('format') === 'pdf'   ? 'selected' : '' }}>PDF</option>
                <option value="excel" {{ request('format') === 'excel' ? 'selected' : '' }}>Excel</option>
            </select>
            @if(request('jenis') || request('format'))
                <a href="{{ route('laporan.riwayat') }}" class="btn btn-ghost" style="font-size:0.8rem;">
                    <i class="ti ti-x"></i> Reset Filter
                </a>
            @endif
            <div style="margin-left:auto;font-size:0.8rem;color:var(--color-neutral-500);align-self:center;">
                {{ $riwayats->total() }} laporan ditemukan
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-wrapper">
        @if($riwayats->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:30px;">#</th>
                    <th>Nama Laporan</th>
                    <th>Jenis</th>
                    <th>Format</th>
                    <th>Dibuat Oleh</th>
                    <th>Tanggal Dibuat</th>
                    <th style="width:90px; text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayats as $i => $laporan)
                <tr>
                    <td class="text-neutral-400">{{ $riwayats->firstItem() + $i }}</td>
                    <td>
                        <div style="font-weight:600;color:var(--color-primary-900,#1B2A4A);font-size:0.875rem;">
                            {{ $laporan->nama_laporan }}
                        </div>
                        @if(!empty($laporan->parameter))
                        <div style="font-size:0.72rem;color:var(--color-neutral-400,#9ca3af);margin-top:2px;">
                            @php
                                $params = collect($laporan->parameter)->filter()->map(function($v, $k) {
                                    return ucfirst(str_replace('_', ' ', $k)) . ': ' . $v;
                                })->implode(' · ');
                            @endphp
                            {{ $params ?: 'Semua data' }}
                        </div>
                        @endif
                    </td>
                    <td>
                        <span class="jenis-badge">
                            <i class="ti {{ $laporan->ikon_jenis }}"></i>
                            {{ $laporan->label_jenis }}
                        </span>
                    </td>
                    <td>
                        <span class="format-badge format-badge-{{ $laporan->format }}">
                            @if($laporan->format === 'pdf')
                                <i class="ti ti-file-type-pdf"></i> PDF
                            @else
                                <i class="ti ti-file-type-xls"></i> Excel
                            @endif
                        </span>
                    </td>
                    <td>
                        <div style="font-size:0.85rem;">{{ $laporan->pembuat?->name ?? '-' }}</div>
                        <div style="font-size:0.72rem;color:var(--color-neutral-400);">{{ $laporan->pembuat?->nik ?? '' }}</div>
                    </td>
                    <td style="font-size:0.82rem;color:var(--color-neutral-600);">
                        {{ $laporan->created_at->isoFormat('D MMM Y') }}<br>
                        <span style="color:var(--color-neutral-400);font-size:0.72rem;">
                            {{ $laporan->created_at->format('H:i') }} WIB
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;justify-content:center;">
                            {{-- Download --}}
                            <a href="{{ route('laporan.download', $laporan->id) }}"
                               class="action-btn action-btn-download"
                               title="Download Laporan">
                                <i class="ti ti-download"></i>
                            </a>
                            {{-- Hapus --}}
                            <button type="button"
                                    class="action-btn action-btn-delete"
                                    title="Hapus Riwayat"
                                    @click="deleteUrl = '{{ route('laporan.destroy', $laporan->id) }}';
                                            deleteName = '{{ addslashes($laporan->nama_laporan) }}';
                                            showDeleteModal = true">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($riwayats->hasPages())
        <div class="table-pagination-wrapper">
            {{ $riwayats->links() }}
        </div>
        @endif

        @else
        {{-- Empty State --}}
        <div class="empty-state">
            <i class="ti ti-file-off"></i>
            <p>Belum ada laporan yang pernah dibuat.</p>
            <a href="{{ route('laporan.index') }}" class="btn btn-primary d-flex align-center gap-2" style="display:inline-flex;">
                <i class="ti ti-plus"></i> Buat Laporan Pertama
            </a>
        </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL KONFIRMASI HAPUS --}}
    {{-- ============================================================ --}}
    <div x-show="showDeleteModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display:none; position:fixed; inset:0; z-index:9999; overflow-y:auto;"
         @keydown.escape.window="showDeleteModal = false">

        <div style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px;">
            {{-- Backdrop --}}
            <div style="position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(2px);"
                 @click="showDeleteModal = false"></div>

            {{-- Modal Box --}}
            <div x-show="showDeleteModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 style="position:relative; background:white; border-radius:16px; padding:28px; max-width:420px; width:100%;
                        box-shadow:0 20px 60px rgba(0,0,0,0.15); z-index:10000;">

                {{-- Icon --}}
                <div style="width:56px;height:56px;background:#fee2e2;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="ti ti-trash" style="font-size:26px;color:#dc2626;"></i>
                </div>

                <h3 style="text-align:center;font-size:1.1rem;font-weight:700;color:#1B2A4A;margin-bottom:8px;">
                    Hapus Riwayat Laporan
                </h3>
                <p style="text-align:center;font-size:0.85rem;color:#6b7280;margin-bottom:6px;">
                    Anda akan menghapus:
                </p>
                <p style="text-align:center;font-size:0.875rem;font-weight:600;color:#1B2A4A;margin-bottom:6px;" x-text="deleteName"></p>
                <p style="text-align:center;font-size:0.8rem;color:#dc2626;margin-bottom:24px;">
                    File akan dihapus permanen dari server dan tidak dapat dipulihkan.
                </p>

                <div style="display:flex;gap:10px;">
                    <button type="button"
                            @click="showDeleteModal = false"
                            style="flex:1;padding:11px;border:1px solid #e5e7eb;border-radius:10px;background:white;font-weight:600;font-size:0.875rem;cursor:pointer;color:#374151;">
                        Batal
                    </button>
                    <form :action="deleteUrl" method="POST" style="flex:1;">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                style="width:100%;padding:11px;border:none;border-radius:10px;background:#dc2626;color:white;font-weight:600;font-size:0.875rem;cursor:pointer;">
                            <i class="ti ti-trash" style="margin-right:6px;"></i> Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
