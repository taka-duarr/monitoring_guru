@extends('layouts.admin')

@section('title', 'Status Kelas Live - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
    <style>
        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--color-success-500);
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(var(--color-success-500), 0.7);
            animation: pulse-animation 1.6s infinite cubic-bezier(0.66, 0, 0, 1);
        }
        @keyframes pulse-animation {
            to {
                box-shadow: 0 0 0 8px rgba(34, 197, 94, 0);
            }
        }
        .gray-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--color-neutral-400);
            display: inline-block;
        }
    </style>
@endpush

@section('content')
<div class="position-relative">
    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Status Aktivitas Kelas</h2>
            <p class="text-sm text-neutral-500">Pantau aktivitas belajar-mengajar di setiap ruangan secara real-time</p>
        </div>
        <div class="d-flex align-center gap-3">
            {{-- Countdown refresh badge --}}
            <div class="d-flex align-center gap-1.5 text-xs text-neutral-500 bg-neutral-50 border border-neutral-200 px-3 py-1.5 rounded-xl">
                <i class="ti ti-refresh" style="font-size:13px;"></i>
                <span>Refresh dalam <span id="refresh-countdown" class="font-semibold text-neutral-700">30</span>d</span>
            </div>

        </div>
    </div>

    {{-- Summary bar --}}
    @php
        $aktif = $data->filter(fn($k) => $k->live_status && $k->live_status->is_active)->count();
        $total = $data->total();
    @endphp
    @if(!$data->isEmpty())
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 mb-4 text-sm text-emerald-700 d-flex align-center gap-2">
            <span class="pulse-dot" style="flex-shrink:0;"></span>
            <span>
                <span class="font-bold">{{ $aktif }} kelas aktif</span> dari <span class="font-bold">{{ $total }} total kelas</span> saat ini sedang berlangsung
            </span>
        </div>
    @endif

    <!-- MAIN DATA TABLE SECTION -->
    <div class="table-wrapper card p-0 overflow-hidden" x-data="{ tableLoading: false }">
        <div class="table-loading-overlay" x-show="tableLoading" style="display: none;">
            <div class="table-spinner"></div>
        </div>

        @if($data->isEmpty())
            <div class="table-empty-state">
                <div class="table-empty-icon">
                    <i class="ti ti-device-laptop text-primary"></i>
                </div>
                <span class="table-empty-title">Tidak ada data kelas</span>
                <span class="table-empty-sub">Belum ada daftar kelas yang ditambahkan.</span>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Kelas</th>
                        <th>Ruangan</th>
                        <th>Mata Pelajaran</th>
                        <th>Pengajar</th>
                        <th class="col-center">Status</th>
                        <th class="col-actions col-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        @php
                            $status = $row->live_status;
                            $isActive = $status ? $status->is_active : false;
                        @endphp
                        <tr class="{{ $isActive ? 'bg-emerald-50/50' : '' }}" style="{{ $isActive ? 'border-left: 4px solid #16a34a;' : '' }}">
                            <td class="col-no">
                                {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                            </td>
                            <td>
                                <span class="font-bold text-neutral-900">{{ $row->name }}</span>
                                @if($row->grade)
                                    <span class="text-xs text-neutral-500 d-block">Tingkat {{ $row->grade }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="font-medium text-neutral-700">{{ $isActive ? ($status->ruangan ?? '-') : '-' }}</span>
                            </td>
                            <td class="{{ $isActive ? 'text-neutral-800 font-semibold' : 'text-neutral-400 italic' }}">
                                {{ $isActive ? ($status->mapel ?? '-') : 'Tidak ada sesi' }}
                            </td>
                            <td class="{{ $isActive ? 'text-neutral-800' : 'text-neutral-400 italic' }}">
                                {{ $isActive ? ($status->pengajar ?? '-') : '-' }}
                            </td>
                            <td class="col-center">
                                @if($isActive)
                                    <span class="badge badge-success d-inline-flex align-center gap-1.5" style="font-size: 0.8rem; padding: 4px 10px;">
                                        <span class="pulse-dot"></span>
                                        Sedang Belajar
                                    </span>
                                @else
                                    <span class="badge badge-neutral d-inline-flex align-center gap-1.5">
                                        <span class="gray-dot"></span>
                                        Kosong
                                    </span>
                                @endif
                            </td>
                            <td class="col-actions col-center">
                                @if($status)
                                    <div class="action-buttons-group">
                                        <!-- Edit Record -->
                                        <x-tooltip text="Edit Log">
                                            <a href="{{ route('statuskelas.edit', $status->id) }}" class="btn btn-ghost action-edit">
                                                <i class="ti ti-pencil"></i>
                                            </a>
                                        </x-tooltip>
                                        
                                        <!-- Reset / Delete Button -->
                                        <x-tooltip text="Reset Status">
                                            <button type="button" class="btn btn-ghost action-delete" 
                                                    @click="$dispatch('confirm-delete', {
                                                        url: '{{ route('statuskelas.destroy', $status->id) }}',
                                                        name: 'Log kelas {{ addslashes($row->name) }}'
                                                    })">
                                                <i class="ti ti-rotate"></i>
                                            </button>
                                        </x-tooltip>
                                    </div>
                                @else
                                    <span class="text-neutral-400 italic text-xs">Belum ada aktivitas</span>
                                @endif
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

@push('scripts')
<script>
    (function () {
        var seconds = 30;
        var el = document.getElementById('refresh-countdown');

        if (!el) return;

        var interval = setInterval(function () {
            seconds--;
            el.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(interval);
                location.reload();
            }
        }, 1000);
    })();
</script>
@endpush

@endsection