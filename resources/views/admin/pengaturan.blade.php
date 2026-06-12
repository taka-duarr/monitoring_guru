@extends('layouts.admin')

@section('title', 'Pengaturan Sistem - SIMGURU')

@section('content')
<div class="position-relative">
    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Pengaturan Sistem</h2>
            <p class="text-sm text-neutral-500">Konfigurasi profil sekolah dan tahun ajaran aktif</p>
        </div>
    </div>

    <!-- Alert Success / Error (jika ada session success) -->
    @if(session('success'))
        <div class="mb-4 p-4 rounded-lg bg-success-50 border border-success-100 text-success-700 text-sm d-flex align-center gap-2">
            <i class="ti ti-circle-check text-lg"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 rounded-lg bg-danger-50 border border-danger-100 text-danger-700 text-sm">
            <div class="font-semibold mb-1 d-flex align-center gap-2">
                <i class="ti ti-alert-circle text-lg"></i>
                Ada kesalahan pengisian form:
            </div>
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex flex-column gap-4">
        <!-- Form Card Container -->
        <div class="card p-6 rounded-lg mt-0 shadow-sm bg-white">
            <form action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- PROFIL SEKOLAH -->
                <div>
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 pb-2 border-b border-neutral-100">Informasi Identitas Sekolah</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column: Text Inputs -->
                        <div class="d-flex flex-column gap-4">
                            <div class="form-group">
                                <label class="form-label" for="school_name">Nama Sekolah <span class="text-danger">*</span></label>
                                <input type="text" name="school_name" id="school_name" class="form-control" value="{{ old('school_name', $settings['school_name']) }}" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="school_address">Alamat Sekolah <span class="text-danger">*</span></label>
                                <textarea name="school_address" id="school_address" class="form-control" rows="3" required>{{ old('school_address', $settings['school_address']) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="school_phone">Nomor Telepon</label>
                                <input type="text" name="school_phone" id="school_phone" class="form-control" value="{{ old('school_phone', $settings['school_phone']) }}">
                            </div>
                        </div>

                        <!-- Right Column: Headmaster & Logo -->
                        <div class="d-flex flex-column gap-4">
                            <div class="form-group">
                                <label class="form-label" for="headmaster_name">Nama Kepala Sekolah</label>
                                <input type="text" name="headmaster_name" id="headmaster_name" class="form-control" value="{{ old('headmaster_name', $settings['headmaster_name']) }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="headmaster_nip">NIP Kepala Sekolah</label>
                                <input type="text" name="headmaster_nip" id="headmaster_nip" class="form-control" value="{{ old('headmaster_nip', $settings['headmaster_nip']) }}">
                            </div>

                            <!-- Logo Upload -->
                            <div class="form-group">
                                <label class="form-label">Logo Sekolah</label>
                                <div class="d-flex align-center gap-4 mt-1">
                                    <div class="flex-shrink-0" style="width: 70px; height: 70px; border-radius: 8px; border: 2px dashed #cbd5e1; display: flex; align-items: center; justify-content: center; background: #f8fafc; overflow: hidden;">
                                        @if($settings['school_logo'])
                                            <img src="{{ asset('storage/' . $settings['school_logo']) }}" alt="Logo Sekolah" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <span class="text-xs text-neutral-400 font-bold">No Logo</span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" name="school_logo" id="school_logo" class="form-control" accept="image/*" style="font-size: 13px; padding: 6px 12px;">
                                        <span class="text-xs text-neutral-400 mt-1 d-block">Format: JPG, JPEG, PNG (Maks 2MB)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PERIODE AKADEMIK -->
                <div class="mt-8 pt-6 border-t border-neutral-100">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 pb-2 border-b border-neutral-100">Periode Akademik Aktif</h3>
                    <p class="text-sm text-neutral-500 mb-4">Tentukan rentang tanggal untuk tahun ajaran / semester aktif saat ini. Data di luar rentang ini akan dianggap sebagai histori.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label class="form-label" for="academic_year_name">Nama Tahun Ajaran <span class="text-danger">*</span></label>
                            <input type="text" name="academic_year_name" id="academic_year_name" class="form-control" value="{{ old('academic_year_name', $settings['academic_year_name']) }}" placeholder="Misal: 2023/2024 Genap" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="academic_year_start">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="academic_year_start" id="academic_year_start" class="form-control" value="{{ old('academic_year_start', $settings['academic_year_start']) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="academic_year_end">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="academic_year_end" id="academic_year_end" class="form-control" value="{{ old('academic_year_end', $settings['academic_year_end']) }}" required>
                        </div>
                    </div>
                </div>

                <!-- Form Submit Footer -->
                <div class="mt-8 pt-4 border-t border-neutral-100 d-flex justify-end gap-2">
                    <button type="submit" class="btn btn-primary d-flex align-center gap-2 px-6">
                        <i class="ti ti-device-floppy text-base"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
