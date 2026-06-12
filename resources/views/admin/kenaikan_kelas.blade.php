@extends('layouts.admin')

@section('title', 'Kenaikan Kelas & Kelulusan - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')
<div x-data="{ 
    selectedAction: 'naik_tingkat',
    selectAll: false,
    toggleAll() {
        const checkboxes = document.querySelectorAll('.kelas-checkbox');
        checkboxes.forEach(cb => cb.checked = this.selectAll);
    }
}" class="position-relative">

    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Kenaikan Kelas & Kelulusan</h2>
            <p class="text-sm text-neutral-500">Pilih kelas untuk dinaikkan tingkatnya (contoh: Kelas 10 ke 11) atau diluluskan.</p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="mb-4 p-4 rounded-lg bg-success-50 border border-success-100 text-success-700 text-sm d-flex align-center gap-2">
            <i class="ti ti-circle-check text-lg"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 rounded-lg bg-danger-50 border border-danger-100 text-danger-700 text-sm">
            <div class="font-semibold mb-2 d-flex align-center gap-2">
                <i class="ti ti-alert-triangle text-lg text-danger-600"></i>
                Gagal memproses data! Periksa isian Anda:
            </div>
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card p-0 overflow-hidden shadow-sm bg-white">
        <form action="{{ route('kenaikan_kelas.proses') }}" method="POST">
            @csrf
            
            <div class="p-4 border-b border-neutral-100 d-flex justify-between align-center" style="background-color: #f8fafc;">
                <h3 class="text-base font-semibold text-primary-900">Pilih Kelas & Aksi</h3>
                
                <div class="d-flex align-center gap-4">
                    <select name="action" class="form-select w-auto" x-model="selectedAction" style="padding-top: 6px; padding-bottom: 6px; height: auto;">
                        <option value="naik_tingkat">Naik Kelas (Naik)</option>
                        <option value="luluskan">Luluskan Kelas (Alumni)</option>
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm d-flex align-center gap-2" onclick="return confirm('Apakah Anda yakin ingin memproses data kelas yang dipilih? Pastikan Anda sudah mengeceknya dua kali.')">
                        <i class="ti ti-device-floppy"></i> Proses Terpilih
                    </button>
                </div>
            </div>

            @if($kelasList->isEmpty())
                <div class="p-6 text-center text-neutral-500">
                    Tidak ada kelas aktif yang tersedia.
                </div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">
                                <input type="checkbox" x-model="selectAll" @change="toggleAll" style="width: 16px; height: 16px;">
                            </th>
                            <th class="col-no">No</th>
                            <th>Nama Kelas</th>
                            <th>Tingkatan</th>
                            <th>Jumlah Siswa Aktif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelasList as $row)
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="kelas_ids[]" value="{{ $row->id }}" class="kelas-checkbox" style="width: 16px; height: 16px;">
                                </td>
                                <td class="col-no">{{ $loop->iteration }}</td>
                                <td><span class="font-bold text-primary-900">{{ $row->name }}</span></td>
                                <td>Kelas {{ $row->grade }}</td>
                                <td>
                                    <span class="badge bg-neutral-50 text-neutral-700 border border-neutral-200" style="padding: 4px 8px; border-radius: 12px; font-size: 11px;">
                                        {{ $row->murids_count }} Siswa
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </form>
    </div>

</div>
@endsection
