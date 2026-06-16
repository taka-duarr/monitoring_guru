@extends('layouts.admin')
@section('title', 'Manajemen Tahun Ajaran - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')
<div x-data="{ showTambah: false, showEdit: false, editId: '', editTahunMulai: '', editTahunSelesai: '', editSemester: '' }">

    <!-- Header -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Manajemen Tahun Ajaran</h2>
            <p class="text-sm text-neutral-500">Kelola tahun ajaran aktif. Jadwal ajar terhubung ke tahun ajaran.</p>
        </div>
        <button type="button" class="btn btn-primary d-flex align-center gap-2" @click="showTambah = true">
            <i class="ti ti-plus"></i> Tambah Tahun Ajaran
        </button>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-lg bg-success-50 border border-success-100 text-success-700 text-sm d-flex align-center gap-2">
            <i class="ti ti-circle-check text-lg"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 rounded-lg bg-danger-50 border border-danger-100 text-danger-700 text-sm d-flex align-center gap-2">
            <i class="ti ti-alert-circle text-lg"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Info banner -->
    <div class="mb-4 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-sm d-flex align-center gap-3">
        <i class="ti ti-info-circle text-lg flex-shrink-0"></i>
        <span>Tahun Ajaran yang <strong>Aktif</strong> akan menjadi default saat menambah jadwal ajar baru. Jadwal ajar lama tetap terhubung ke tahun ajaran masing-masing.</span>
    </div>

    <!-- Tabel -->
    <div class="table-wrapper card p-0 overflow-hidden">
        @if($data->isEmpty())
            <div class="table-empty-state">
                <div class="table-empty-icon"><i class="ti ti-calendar text-primary"></i></div>
                <span class="table-empty-title">Belum ada data tahun ajaran</span>
                <div class="table-empty-actions">
                    <button type="button" class="btn btn-primary" @click="showTambah = true">Tambah Sekarang</button>
                </div>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Tahun Ajaran</th>
                        <th class="col-center">Semester</th>
                        <th class="col-center">Status</th>
                        <th class="col-center">Jumlah Jadwal</th>
                        <th class="col-actions col-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                    <tr class="{{ $row->is_active ? 'bg-success-50/30' : '' }}">
                        <td>
                            <span class="font-bold text-neutral-800">{{ $row->name }}</span>
                            @if($row->is_active)
                                <span class="ml-2 inline-flex items-center gap-1 text-xs font-bold text-success-700 bg-success-100 border border-success-200 px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-success-500 animate-pulse inline-block"></span> Aktif
                                </span>
                            @endif
                        </td>
                        <td class="col-center">
                            <span class="badge {{ $row->semester === 'Ganjil' ? 'bg-primary-50 text-primary-700 border border-primary-200' : 'bg-warning-50 text-warning-700 border border-warning-200' }}"
                                  style="padding:4px 10px;border-radius:12px;font-size:12px;">
                                {{ $row->semester }}
                            </span>
                        </td>
                        <td class="col-center">
                            @if($row->is_active)
                                <span class="badge bg-success-50 text-success-700 border border-success-200" style="padding:4px 10px;border-radius:12px;font-size:12px;">Aktif</span>
                            @else
                                <span class="badge bg-neutral-100 text-neutral-500 border border-neutral-200" style="padding:4px 10px;border-radius:12px;font-size:12px;">Nonaktif</span>
                            @endif
                        </td>
                        <td class="col-center font-semibold text-neutral-700">
                            {{ $row->jadwalAjars()->count() }}
                        </td>
                        <td class="col-actions col-center">
                            <div class="action-buttons-group">
                                @if(!$row->is_active)
                                <form action="{{ route('tahun-ajaran.aktif', $row->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <x-tooltip text="Jadikan Aktif">
                                        <button type="submit" class="btn btn-ghost action-edit" title="Jadikan Aktif">
                                            <i class="ti ti-check text-success-600"></i>
                                        </button>
                                    </x-tooltip>
                                </form>
                                @endif
                                <x-tooltip text="Edit">
                                    <button type="button" class="btn btn-ghost action-edit"
                                        @click="showEdit = true; editId = '{{ $row->id }}'; editTahunMulai = '{{ $row->tahun_mulai }}'; editTahunSelesai = '{{ $row->tahun_selesai }}'; editSemester = '{{ $row->semester }}'">
                                        <i class="ti ti-pencil"></i>
                                    </button>
                                </x-tooltip>
                                @if(!$row->is_active)
                                <x-tooltip text="Hapus">
                                    <button type="button" class="btn btn-ghost action-delete"
                                        @click="$dispatch('confirm-delete', { url: '{{ route('tahun-ajaran.destroy', $row->id) }}', name: '{{ $row->name }}' })">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </x-tooltip>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <x-modal-hapus />

    <!-- MODAL TAMBAH -->
    <div class="modal-backdrop-blur" x-show="showTambah" x-transition style="display:none;position:fixed;inset:0;z-index:100;background-color:rgba(15,23,42,0.6);backdrop-filter:blur(4px);">
        <div style="display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px;">
            <div class="card bg-white shadow-xl" style="width:100%;max-width:480px;padding:24px;border-radius:12px;" @click.away="showTambah = false">
                <div class="d-flex align-center justify-between pb-3 mb-4" style="border-bottom:1px solid var(--color-neutral-100);">
                    <h3 class="text-lg font-bold text-primary-900" style="margin:0;">Tambah Tahun Ajaran</h3>
                    <button type="button" class="btn btn-ghost p-1" @click="showTambah = false"><i class="ti ti-x" style="font-size:20px;"></i></button>
                </div>
                <form action="{{ route('tahun-ajaran.store') }}" method="POST">
                    @csrf
                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex gap-3">
                            <div class="form-group mb-0 flex-1">
                                <label class="form-label">Tahun Mulai <span class="required-indicator">*</span></label>
                                <input type="number" name="tahun_mulai" class="form-control" placeholder="2025" min="2000" max="2100" required>
                            </div>
                            <div class="form-group mb-0 flex-1">
                                <label class="form-label">Tahun Selesai <span class="required-indicator">*</span></label>
                                <input type="number" name="tahun_selesai" class="form-control" placeholder="2026" min="2000" max="2100" required>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label">Semester <span class="required-indicator">*</span></label>
                            <select name="semester" class="form-select" required>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-end gap-2 mt-6 pt-3" style="border-top:1px solid var(--color-neutral-100);">
                        <button type="button" class="btn btn-secondary" @click="showTambah = false">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="modal-backdrop-blur" x-show="showEdit" x-transition style="display:none;position:fixed;inset:0;z-index:100;background-color:rgba(15,23,42,0.6);backdrop-filter:blur(4px);">
        <div style="display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px;">
            <div class="card bg-white shadow-xl" style="width:100%;max-width:480px;padding:24px;border-radius:12px;" @click.away="showEdit = false">
                <div class="d-flex align-center justify-between pb-3 mb-4" style="border-bottom:1px solid var(--color-neutral-100);">
                    <h3 class="text-lg font-bold text-primary-900" style="margin:0;">Edit Tahun Ajaran</h3>
                    <button type="button" class="btn btn-ghost p-1" @click="showEdit = false"><i class="ti ti-x" style="font-size:20px;"></i></button>
                </div>
                <form :action="'/admin/tahun-ajaran/' + editId" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex gap-3">
                            <div class="form-group mb-0 flex-1">
                                <label class="form-label">Tahun Mulai <span class="required-indicator">*</span></label>
                                <input type="number" name="tahun_mulai" class="form-control" x-model="editTahunMulai" min="2000" max="2100" required>
                            </div>
                            <div class="form-group mb-0 flex-1">
                                <label class="form-label">Tahun Selesai <span class="required-indicator">*</span></label>
                                <input type="number" name="tahun_selesai" class="form-control" x-model="editTahunSelesai" min="2000" max="2100" required>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label">Semester <span class="required-indicator">*</span></label>
                            <select name="semester" class="form-select" x-model="editSemester" required>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-end gap-2 mt-6 pt-3" style="border-top:1px solid var(--color-neutral-100);">
                        <button type="button" class="btn btn-secondary" @click="showEdit = false">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
