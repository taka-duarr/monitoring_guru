@extends('layouts.admin')

@section('title', 'Pengaturan Sistem - SIMGURU')

@section('content')
<div class="position-relative" x-data="{ activeTab: 'profil' }">
    <!-- Header Page Title -->
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Pengaturan Sistem</h2>
            <p class="text-sm text-neutral-500">Konfigurasi profil sekolah, tahun ajaran aktif, dan parameter absensi guru</p>
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
        <!-- Tabs Navigation -->
        <div class="d-flex border-b border-neutral-200 bg-white rounded-t-lg px-4 pt-2 gap-2 shadow-xs">
            <button type="button" 
                    @click="activeTab = 'profil'" 
                    class="px-4 py-3 font-medium text-sm border-b-2 transition-all d-flex align-center gap-2"
                    :class="activeTab === 'profil' ? 'border-primary-600 text-primary-600' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300'">
                <i class="ti ti-school"></i> Profil Sekolah
            </button>
            <button type="button" 
                    @click="activeTab = 'akademik'" 
                    class="px-4 py-3 font-medium text-sm border-b-2 transition-all d-flex align-center gap-2"
                    :class="activeTab === 'akademik' ? 'border-primary-600 text-primary-600' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300'">
                <i class="ti ti-clock"></i> Akademik & Absensi
            </button>
        </div>

        <!-- Form Card Container -->
        <div class="card p-6 rounded-b-lg rounded-t-none mt-0 shadow-sm bg-white">
            <form action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- TAB 1: PROFIL SEKOLAH -->
                <div x-show="activeTab === 'profil'" x-transition>
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

                <!-- TAB 2: AKADEMIK & ABSENSI -->
                <div x-show="activeTab === 'akademik'" x-transition style="display: none;">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 pb-2 border-b border-neutral-100">Parameter Akademik & Jam Kerja</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column: Periode Akademik -->
                        <div class="d-flex flex-column gap-4">
                            <h4 class="font-medium text-sm text-neutral-700 uppercase tracking-wider mb-1">Periode Aktif</h4>
                            
                            <div class="form-group">
                                <label class="form-label" for="academic_year">Tahun Ajaran <span class="text-danger">*</span></label>
                                <input type="text" name="academic_year" id="academic_year" class="form-control" placeholder="Contoh: 2025/2026" value="{{ old('academic_year', $settings['academic_year']) }}" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="academic_semester">Semester Aktif <span class="text-danger">*</span></label>
                                <select name="academic_semester" id="academic_semester" class="form-control" style="color: #111827; background: #ffffff;" required>
                                    <option value="Ganjil" {{ old('academic_semester', $settings['academic_semester']) === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ old('academic_semester', $settings['academic_semester']) === 'Genap' ? 'selected' : '' }}>Genap</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right Column: Parameter Absensi -->
                        <div class="d-flex flex-column gap-4">
                            <h4 class="font-medium text-sm text-neutral-700 uppercase tracking-wider mb-1">Jadwal Kehadiran Standar</h4>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="form-label" for="default_time_in">Jam Masuk <span class="text-danger">*</span></label>
                                    <input type="time" name="default_time_in" id="default_time_in" class="form-control" value="{{ old('default_time_in', $settings['default_time_in']) }}" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="tolerance_minutes">Toleransi Telat (Menit) <span class="text-danger">*</span></label>
                                    <input type="number" name="tolerance_minutes" id="tolerance_minutes" class="form-control" min="0" value="{{ old('tolerance_minutes', $settings['tolerance_minutes']) }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="default_time_out">Jam Keluar <span class="text-danger">*</span></label>
                                <input type="time" name="default_time_out" id="default_time_out" class="form-control" value="{{ old('default_time_out', $settings['default_time_out']) }}" required>
                            </div>
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
