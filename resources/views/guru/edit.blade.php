@extends('layouts.admin')

@section('title', 'Edit Data Guru - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')
<!-- Header Page Title -->
<div class="d-flex align-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-900">Edit Data Guru</h2>
        <p class="text-sm text-neutral-500">Perbarui profil guru dan jadwal pengampu kelas.</p>
    </div>
    <!-- Back Button (Top Right) -->
    <a href="{{ route('guru.index') }}" class="btn btn-secondary d-flex align-center gap-2" style="text-decoration: none;">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white" style="padding: 28px;">
    <!-- Main Form -->
    <form action="{{ route('guru.update', $data->id) }}" method="POST" enctype="multipart/form-data"
          x-data="{ loading: false, statusKepegawaian: '{{ old('status_kepegawaian', $data->status_kepegawaian ?? '') }}' }"
          @submit="loading = true">
        @csrf
        @method('PUT')

        <!-- SECTION 1 — Foto Profil Upload & Preview -->
        <div class="mb-6">
            <h3 style="font-size: 15px; font-weight: 700; color: var(--color-primary-800); border-bottom: 1px solid var(--color-neutral-200); padding-bottom: 8px; margin-bottom: 16px;">
                Foto Profil Guru
            </h3>
            
            <div x-data="{ 
                dragover: false, 
                imagePreview: '{{ $data->foto ? asset('storage/' . $data->foto) : '' }}',
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
                    <div class="photo-upload-preview" x-show="imagePreview">
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
                           class="form-control @error('name') is-invalid @else is-valid @enderror" 
                           value="{{ old('name', $data->name) }}" 
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
                           class="form-control @error('nik') is-invalid @else is-valid @enderror" 
                           value="{{ old('nik', $data->nik) }}" 
                           placeholder="Masukkan 18 digit NIP..."
                           maxlength="18"
                           pattern="[0-9]{18}">
                    <span class="form-helper">NIP akan digunakan sebagai username & password default login guru.</span>
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
                            <input type="radio" id="jk_l" name="jenis_kelamin" value="Laki-laki" {{ old('jenis_kelamin', $data->jenis_kelamin) == 'Laki-laki' ? 'checked' : '' }}>
                            <label for="jk_l" class="gender-radio-label">Laki-laki</label>
                        </div>
                        <div class="gender-radio-card">
                            <input type="radio" id="jk_p" name="jenis_kelamin" value="Perempuan" {{ old('jenis_kelamin', $data->jenis_kelamin) == 'Perempuan' ? 'checked' : '' }}>
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

                <!-- Tempat & Tanggal Lahir -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir" 
                               class="form-control @error('tempat_lahir') is-invalid @else @if(old('tempat_lahir', $data->tempat_lahir)) is-valid @endif @enderror" 
                               value="{{ old('tempat_lahir', $data->tempat_lahir) }}" 
                               placeholder="Kota/Kabupaten...">
                        @error('tempat_lahir')
                            <span class="form-error">
                                <i class="ti ti-alert-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" 
                               class="form-control @error('tanggal_lahir') is-invalid @else @if(old('tanggal_lahir', $data->tanggal_lahir)) is-valid @endif @enderror" 
                               value="{{ old('tanggal_lahir', $data->tanggal_lahir ? \Carbon\Carbon::parse($data->tanggal_lahir)->format('Y-m-d') : '') }}">
                        @error('tanggal_lahir')
                            <span class="form-error">
                                <i class="ti ti-alert-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
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
                               class="form-control @error('no_telp') is-invalid @else @if(old('no_telp', $data->no_telp)) is-valid @endif @enderror" 
                               value="{{ old('no_telp', str_starts_with($data->no_telp ?? '', '+62') ? substr($data->no_telp, 3) : (str_starts_with($data->no_telp ?? '', '62') ? substr($data->no_telp, 2) : $data->no_telp)) }}" 
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

            <!-- RIGHT COLUMN — Data Kepegawaian & Data Mengajar -->
            <div>
                <!-- Data Kepegawaian Section -->
                <h3 style="font-size: 15px; font-weight: 700; color: var(--color-primary-800); border-bottom: 1px solid var(--color-neutral-200); padding-bottom: 8px; margin-bottom: 16px;">
                    Data Kepegawaian
                </h3>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Status Kepegawaian -->
                    <div class="form-group">
                        <label for="status_kepegawaian" class="form-label">
                            Status Pegawai <span class="required-indicator">*</span>
                        </label>
                        <select id="status_kepegawaian" name="status_kepegawaian" 
                                class="form-select @error('status_kepegawaian') is-invalid @enderror"
                                x-model="statusKepegawaian">
                            <option value="">Pilih Status...</option>
                            <option value="PNS">PNS</option>
                            <option value="GTT">GTT (Guru Tidak Tetap)</option>
                            <option value="GTY">GTY (Guru Tetap Yayasan)</option>
                            <option value="Honorer">Honorer</option>
                        </select>
                        @error('status_kepegawaian')
                            <span class="form-error">
                                <i class="ti ti-alert-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Golongan (Conditional) -->
                    <div class="form-group" x-show="statusKepegawaian === 'PNS'" style="display: none;">
                        <label for="golongan" class="form-label">
                            Golongan / Pangkat <span class="required-indicator">*</span>
                        </label>
                        <input type="text" id="golongan" name="golongan" 
                               class="form-control @error('golongan') is-invalid @else @if(old('golongan', $data->golongan)) is-valid @endif @enderror" 
                               value="{{ old('golongan', $data->golongan) }}" 
                               placeholder="Contoh: IV/aPembina">
                        @error('golongan')
                            <span class="form-error">
                                <i class="ti ti-alert-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- TMT -->
                    <div class="form-group">
                        <label for="tmt" class="form-label">TMT (Mulai Tugas)</label>
                        <input type="date" id="tmt" name="tmt" 
                               class="form-control @error('tmt') is-invalid @else @if(old('tmt', $data->tmt)) is-valid @endif @enderror" 
                               value="{{ old('tmt', $data->tmt ? \Carbon\Carbon::parse($data->tmt)->format('Y-m-d') : '') }}">
                        @error('tmt')
                            <span class="form-error">
                                <i class="ti ti-alert-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Status Aktif (3-way selector card style) -->
                    <div class="form-group">
                        <label class="form-label">
                            Status Aktif <span class="required-indicator">*</span>
                        </label>
                        <div class="d-flex gap-2">
                            <!-- Aktif -->
                            <div class="gender-radio-card" style="flex: 1;">
                                <input type="radio" id="status_a" name="status" value="Aktif" {{ old('status', $data->status) == 'Aktif' ? 'checked' : '' }}>
                                <label for="status_a" class="gender-radio-label" style="padding: 10px; font-size: 13px;">Aktif</label>
                            </div>
                            <!-- Cuti -->
                            <div class="gender-radio-card" style="flex: 1;">
                                <input type="radio" id="status_c" name="status" value="Cuti" {{ old('status', $data->status) == 'Cuti' ? 'checked' : '' }}>
                                <label for="status_c" class="gender-radio-label" style="padding: 10px; font-size: 13px;">Cuti</label>
                            </div>
                            <!-- Pensiun -->
                            <div class="gender-radio-card" style="flex: 1;">
                                <input type="radio" id="status_p" name="status" value="Pensiun" {{ old('status', $data->status) == 'Pensiun' ? 'checked' : '' }}>
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

                <!-- Data Mengajar Section -->
                <h3 style="font-size: 15px; font-weight: 700; color: var(--color-primary-800); border-bottom: 1px solid var(--color-neutral-200); padding-bottom: 8px; margin-bottom: 16px; margin-top: 10px;">
                    Data Mengajar
                </h3>

                <!-- Mata Pelajaran -->
                <div class="form-group">
                    <label for="mapel_id" class="form-label">
                        Mata Pelajaran Utama <span class="required-indicator">*</span>
                    </label>
                    <select id="mapel_id" name="mapel_id" class="form-select @error('mapel_id') is-invalid @enderror">
                        <option value="">Pilih Mata Pelajaran...</option>
                        @foreach($mapels as $mapel)
                            <option value="{{ $mapel->id }}" {{ old('mapel_id', $assignedMapelId) == $mapel->id ? 'selected' : '' }}>
                                {{ $mapel->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('mapel_id')
                        <span class="form-error">
                            <i class="ti ti-alert-circle"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <!-- Kelas Pengampu (Multi checkbox grid) -->
                <div class="form-group">
                    <label class="form-label">
                        Kelas Pengampu <span class="required-indicator">*</span>
                    </label>
                    <div class="checkbox-grid">
                        @foreach($kelas as $kelasItem)
                            <label class="checkbox-item">
                                <input type="checkbox" name="kelas_ids[]" value="{{ $kelasItem->id }}"
                                    {{ in_array($kelasItem->id, old('kelas_ids', $assignedKelasIds ?? [])) ? 'checked' : '' }}>
                                <span>{{ $kelasItem->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('kelas_ids')
                        <span class="form-error">
                            <i class="ti ti-alert-circle"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <!-- Jumlah Jam Mengajar -->
                <div class="form-group">
                    <label for="jumlah_jam" class="form-label">
                        Jumlah Jam Mengajar (JP) <span class="required-indicator">*</span>
                    </label>
                    <input type="number" id="jumlah_jam" name="jumlah_jam" 
                           class="form-control @error('jumlah_jam') is-invalid @else is-valid @enderror" 
                           value="{{ old('jumlah_jam', $data->jumlah_jam ?? 0) }}" 
                           min="0" max="48"
                           placeholder="0">
                    <span class="form-helper">Durasi mengajar dalam JP (Jam Pelajaran) per minggu, maksimal 48 JP.</span>
                    @error('jumlah_jam')
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
                <span x-text="loading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
            </button>
        </div>
    </form>
</div>
@endsection
