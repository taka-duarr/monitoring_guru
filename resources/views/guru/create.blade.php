@extends('layouts.admin')

@section('title', 'Tambah Data Guru - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')
<!-- Header Page Title -->
<div class="d-flex align-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-900">Tambah Data Guru</h2>
        <p class="text-sm text-neutral-500">Daftarkan guru baru dan konfigurasikan jadwal pengampu kelas.</p>
    </div>
    <!-- Back Button (Top Right) -->
    <a href="{{ route('guru.index') }}" class="btn btn-secondary d-flex align-center gap-2" style="text-decoration: none;">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white" style="padding: 28px;">
    <!-- Main Form -->
    <form action="{{ route('guru.store') }}" method="POST" enctype="multipart/form-data"
          x-data="{ loading: false }"
          @submit="loading = true">
        @csrf

        <!-- SECTION 1 — Foto Profil Upload & Preview -->
        <div class="mb-6">
            <h3 style="font-size: 15px; font-weight: 700; color: var(--color-primary-800); border-bottom: 1px solid var(--color-neutral-200); padding-bottom: 8px; margin-bottom: 16px;">
                Foto Profil Guru
            </h3>
            
            <div x-data="{ 
                dragover: false, 
                imagePreview: null,
                triggerUpload() { this.$refs.fileInput.click(); },
                handleFileSelect(e) {
                    const files = e.target.files;
                    if (files.length > 0) {
                        this.previewFile(files[0]);
                    }
                },
                handleFileDrop(e) {
                    this.dragover = false;
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        this.$refs.fileInput.files = files;
                        this.previewFile(files[0]);
                    }
                },
                previewFile(file) {
                    if (file.size > 2 * 1024 * 1024) {
                        window.SimguruToast.show('danger', 'Ukuran gambar maksimal adalah 2MB.');
                        this.$refs.fileInput.value = '';
                        this.imagePreview = null;
                        return;
                    }
                    if (!['image/jpeg', 'image/png', 'image/jpg', 'image/webp'].includes(file.type)) {
                        window.SimguruToast.show('danger', 'Format gambar harus berupa JPG, JPEG, PNG, atau WEBP.');
                        this.$refs.fileInput.value = '';
                        this.imagePreview = null;
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }" class="position-relative">
                
                <!-- Drag Drop Box -->
                <div class="photo-upload-container" 
                     :class="{ 'drag-over': dragover }"
                     @dragover.prevent="dragover = true"
                     @dragleave.prevent="dragover = false"
                     @drop.prevent="handleFileDrop($event)"
                     @click="triggerUpload">
                    
                    <input type="file" name="foto" x-ref="fileInput" @change="handleFileSelect" style="display: none;" accept="image/jpeg,image/png,image/jpg,image/webp">
                    
                    <!-- Placeholder -->
                    <div class="photo-upload-placeholder" x-show="!imagePreview">
                        <i class="ti ti-camera"></i>
                        <span>Drag & drop foto di sini atau klik untuk cari</span>
                        <span class="text-xs text-neutral-400 mt-1">Maks. 2MB (JPG, PNG, WEBP)</span>
                    </div>

                    <!-- Image Preview -->
                    <div class="photo-upload-preview" x-show="imagePreview" style="display: none;">
                        <img :src="imagePreview" alt="Preview Foto">
                        <button type="button" class="photo-change-btn" @click.stop="triggerUpload">Ganti Foto</button>
                    </div>
                </div>
            </div>
            @error('foto')
                <div class="text-center">
                    <span class="form-error">
                        <i class="ti ti-alert-circle"></i>
                        {{ $message }}
                    </span>
                </div>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- LEFT COLUMN — Informasi Pribadi -->
            <div>
                <h3 style="font-size: 15px; font-weight: 700; color: var(--color-primary-800); border-bottom: 1px solid var(--color-neutral-200); padding-bottom: 8px; margin-bottom: 16px;">
                    Informasi Pribadi
                </h3>

                <!-- Nama Lengkap -->
                <div class="form-group">
                    <label for="name" class="form-label">
                        Nama Lengkap <span class="required-indicator">*</span>
                    </label>
                    <input type="text" id="name" name="name" 
                           class="form-control @error('name') is-invalid @else @if(old('name')) is-valid @endif @enderror" 
                           value="{{ old('name') }}" 
                           placeholder="Nama lengkap beserta gelar..." 
                           maxlength="100">
                    @error('name')
                        <span class="form-error">
                            <i class="ti ti-alert-circle"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <!-- NIP / NIK -->
                <div class="form-group">
                    <label for="nik" class="form-label">
                        NIP / NIK <span class="required-indicator">*</span>
                    </label>
                    <input type="text" id="nik" name="nik" 
                           class="form-control @error('nik') is-invalid @else @if(old('nik')) is-valid @endif @enderror" 
                           value="{{ old('nik') }}" 
                           placeholder="Masukkan NIK/NIP atau ID pegawai..."
                           maxlength="50">
                    <span class="form-helper">NIP/ID akan digunakan sebagai username & password default login guru.</span>
                    @error('nik')
                        <span class="form-error">
                            <i class="ti ti-alert-circle"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <!-- Jenis Kelamin -->
                <div class="form-group">
                    <label class="form-label">
                        Jenis Kelamin <span class="required-indicator">*</span>
                    </label>
                    <div class="gender-radio-group">
                        <div class="gender-radio-card">
                            <input type="radio" id="jk_l" name="jenis_kelamin" value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'checked' : '' }}>
                            <label for="jk_l" class="gender-radio-label">Laki-laki</label>
                        </div>
                        <div class="gender-radio-card">
                            <input type="radio" id="jk_p" name="jenis_kelamin" value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'checked' : '' }}>
                            <label for="jk_p" class="gender-radio-label">Perempuan</label>
                        </div>
                    </div>
                    @error('jenis_kelamin')
                        <span class="form-error">
                            <i class="ti ti-alert-circle"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>



                <!-- No Telepon -->
                <div class="form-group">
                    <label for="no_telp" class="form-label">No. Telepon / WhatsApp</label>
                    <div class="d-flex align-center position-relative">
                        <span class="bg-neutral-100 border-y border-l border-neutral-300 d-flex align-center px-3" 
                              style="height: 42px; border-top-left-radius: var(--radius-md); border-bottom-left-radius: var(--radius-md); font-size: 14px; font-weight: 600; color: var(--color-neutral-600);">
                            +62
                        </span>
                        <input type="tel" id="no_telp" name="no_telp" 
                               class="form-control @error('no_telp') is-invalid @else @if(old('no_telp')) is-valid @endif @enderror" 
                               value="{{ old('no_telp') }}" 
                               placeholder="812xxxxxxxx"
                               style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                    </div>
                    @error('no_telp')
                        <span class="form-error">
                            <i class="ti ti-alert-circle"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <!-- RIGHT COLUMN — Status Aktif -->
            <div>
                <h3 style="font-size: 15px; font-weight: 700; color: var(--color-primary-800); border-bottom: 1px solid var(--color-neutral-200); padding-bottom: 8px; margin-bottom: 16px;">
                    Status Keaktifan
                </h3>
                
                <div class="form-group">
                    <label class="form-label">
                        Status Aktif <span class="required-indicator">*</span>
                    </label>
                    <div class="d-flex gap-2">
                        <!-- Aktif -->
                        <div class="gender-radio-card" style="flex: 1;">
                            <input type="radio" id="status_a" name="status" value="Aktif" {{ old('status', 'Aktif') == 'Aktif' ? 'checked' : '' }}>
                            <label for="status_a" class="gender-radio-label" style="padding: 10px; font-size: 13px;">Aktif</label>
                        </div>
                        <!-- Cuti -->
                        <div class="gender-radio-card" style="flex: 1;">
                            <input type="radio" id="status_c" name="status" value="Cuti" {{ old('status') == 'Cuti' ? 'checked' : '' }}>
                            <label for="status_c" class="gender-radio-label" style="padding: 10px; font-size: 13px;">Cuti</label>
                        </div>
                        <!-- Pensiun -->
                        <div class="gender-radio-card" style="flex: 1;">
                            <input type="radio" id="status_p" name="status" value="Pensiun" {{ old('status') == 'Pensiun' ? 'checked' : '' }}>
                            <label for="status_p" class="gender-radio-label" style="padding: 10px; font-size: 13px;">Pensiun</label>
                        </div>
                    </div>
                    @error('status')
                        <span class="form-error">
                            <i class="ti ti-alert-circle"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- FORM ACTION BUTTONS (Right-aligned) -->
        <div class="d-flex justify-end gap-3 mt-8 border-t border-neutral-200 pt-5">
            <a href="{{ route('guru.index') }}" class="btn btn-secondary d-flex align-center gap-2" :disabled="loading" style="text-decoration: none;">
                Batal
            </a>
            
            <button type="submit" class="btn btn-primary d-flex align-center gap-2" :disabled="loading">
                <!-- Loading indicator -->
                <template x-if="loading">
                    <span class="table-spinner" style="width: 14px; height: 14px; border-width: 2px; border-color: white; border-top-color: transparent;"></span>
                </template>
                <span x-text="loading ? 'Menyimpan...' : 'Simpan Data'"></span>
            </button>
        </div>
    </form>
</div>
@endsection
