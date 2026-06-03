@extends('layouts.admin')

@section('title', (isset($data) ? 'Edit' : 'Tambah') . ' Pengguna - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')
<!-- Header Page Title -->
<div class="d-flex align-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-900">{{ isset($data) ? 'Edit' : 'Tambah' }} Akun Pengguna</h2>
        <p class="text-sm text-neutral-500">Sesuaikan informasi kredensial dan hak akses akun di bawah ini.</p>
    </div>
    <!-- Back Button -->
    <a href="{{ route('users.index') }}" class="btn btn-secondary d-flex align-center gap-2" style="text-decoration: none;">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white" style="padding: 28px; max-width: 800px; margin: 0 auto;" 
     x-data="{ 
         role: '{{ old('jabatan', $data->jabatan ?? 'admin') }}',
         loading: false 
     }">
    
    <!-- Main Form -->
    <form action="{{ isset($data) ? route('users.update', $data->id) : route('users.store') }}" method="POST"
          @submit="loading = true">
        @csrf
        @if(isset($data)) 
            @method('PUT') 
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <!-- Nama Lengkap -->
            <div class="form-group col-span-2 md:col-span-1">
                <label for="name" class="form-label">
                    Nama Lengkap <span class="required-indicator">*</span>
                </label>
                <input type="text" id="name" name="name" 
                       class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name', $data->name ?? '') }}" placeholder="Contoh: Budi Santoso..." required>
                @error('name')
                    <span class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            <!-- NIK / Username -->
            <div class="form-group col-span-2 md:col-span-1">
                <label for="nik" class="form-label">
                    NIK / NIS / Username <span class="required-indicator">*</span>
                </label>
                <input type="text" id="nik" name="nik" 
                       class="form-control @error('nik') is-invalid @enderror" 
                       value="{{ old('nik', $data->nik ?? '') }}" placeholder="Contoh: 19820304..." required>
                <span class="form-helper">Digunakan sebagai pengenal utama untuk login ke dalam sistem.</span>
                @error('nik')
                    <span class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            <!-- Jabatan (Role) -->
            <div class="form-group col-span-2 md:col-span-1">
                <label for="jabatan" class="form-label">
                    Jabatan (Role) <span class="required-indicator">*</span>
                </label>
                <select id="jabatan" name="jabatan" class="form-select @error('jabatan') is-invalid @enderror" 
                        style="color: #111827; background: #ffffff;" x-model="role" required>
                    <option value="admin">Admin / Pengelola</option>
                    <option value="guru">Guru / Pengajar</option>
                    <option value="ketuakelas">Ketua Kelas</option>
                </select>
                @error('jabatan')
                    <span class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            <!-- Status Akun -->
            <div class="form-group col-span-2 md:col-span-1">
                <label for="status" class="form-label">
                    Status Akun <span class="required-indicator">*</span>
                </label>
                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" 
                        style="color: #111827; background: #ffffff;" required>
                    <option value="Aktif" {{ old('status', $data->status ?? 'Aktif') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Cuti" {{ old('status', $data->status ?? 'Aktif') === 'Cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="Pensiun" {{ old('status', $data->status ?? 'Aktif') === 'Pensiun' ? 'selected' : '' }}>Pensiun</option>
                </select>
                @error('status')
                    <span class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            <!-- Detail Khusus: Kelas (Conditional via AlpineJS) -->
            <div class="form-group col-span-2" x-show="role === 'ketuakelas'" x-transition style="display: none;">
                <label for="kelas_id" class="form-label">
                    Kelas Diampu / Diwakili <span class="required-indicator">*</span>
                </label>
                <select id="kelas_id" name="kelas_id" class="form-select @error('kelas_id') is-invalid @enderror" 
                        style="color: #111827; background: #ffffff;" :required="role === 'ketuakelas'">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ old('kelas_id', $data->kelas_id ?? '') == $k->id ? 'selected' : '' }}>
                            {{ $k->name }}
                        </option>
                    @endforeach
                </select>
                <span class="form-helper">Wajib dipilih apabila jabatan yang diberikan adalah Ketua Kelas.</span>
                @error('kelas_id')
                    <span class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            <!-- Jenis Kelamin -->
            <div class="form-group col-span-2 md:col-span-1">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                <select id="jenis_kelamin" name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" 
                        style="color: #111827; background: #ffffff;">
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="L" {{ old('jenis_kelamin', $data->jenis_kelamin ?? '') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin', $data->jenis_kelamin ?? '') === 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin')
                    <span class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            <!-- Nomor Telepon -->
            <div class="form-group col-span-2 md:col-span-1">
                <label for="no_telp" class="form-label">Nomor Telepon</label>
                <input type="text" id="no_telp" name="no_telp" 
                       class="form-control @error('no_telp') is-invalid @enderror" 
                       value="{{ old('no_telp', $data->no_telp ?? '') }}" placeholder="Contoh: 0812345678...">
                @error('no_telp')
                    <span class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group col-span-2 md:col-span-1">
                <label for="password" class="form-label">
                    Password {{ isset($data) ? '(Kosongkan jika tidak diubah)' : '' }} <span class="required-indicator">{{ isset($data) ? '' : '*' }}</span>
                </label>
                <input type="password" id="password" name="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       placeholder="Minimal 4 karakter..." {{ isset($data) ? '' : 'required' }}>
                @error('password')
                    <span class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            <!-- Konfirmasi Password -->
            <div class="form-group col-span-2 md:col-span-1">
                <label for="password_confirmation" class="form-label">
                    Konfirmasi Password <span class="required-indicator">{{ isset($data) ? '' : '*' }}</span>
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation" 
                       class="form-control" 
                       placeholder="Ulangi password..." {{ isset($data) ? '' : 'required' }}>
            </div>
        </div>

        <!-- FORM ACTION BUTTONS -->
        <div class="d-flex justify-end gap-3 mt-8 border-t border-neutral-200 pt-5">
            <a href="{{ route('users.index') }}" class="btn btn-secondary d-flex align-center gap-2" :disabled="loading" style="text-decoration: none;">
                Batal
            </a>
            
            <button type="submit" class="btn btn-primary d-flex align-center gap-2" :disabled="loading">
                <template x-if="loading">
                    <span class="table-spinner" style="width: 14px; height: 14px; border-width: 2px; border-color: white; border-top-color: transparent;"></span>
                </template>
                <span x-text="loading ? 'Menyimpan...' : 'Simpan Data'"></span>
            </button>
        </div>
    </form>
</div>
@endsection
