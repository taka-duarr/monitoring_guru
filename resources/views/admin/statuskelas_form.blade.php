@extends('layouts.admin')

@section('title', (isset($data) ? 'Edit' : 'Tambah') . ' Status Kelas - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')
<!-- Header Page Title -->
<div class="d-flex align-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-900">{{ isset($data) ? 'Edit' : 'Tambah' }} Status Kelas</h2>
        <p class="text-sm text-neutral-500">Sesuaikan data log aktivitas kelas di bawah ini.</p>
    </div>
    <!-- Back Button -->
    <a href="{{ route('statuskelas.index') }}" class="btn btn-secondary d-flex align-center gap-2" style="text-decoration: none;">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white" style="padding: 28px; max-w-2xl; margin: 0 auto;">
    <!-- Main Form -->
    <form action="{{ isset($data) ? route('statuskelas.update', $data->id) : route('statuskelas.store') }}" method="POST"
          x-data="{ loading: false }"
          @submit="loading = true">
        @csrf
        @if(isset($data)) @method('PUT') @endif

        <!-- Mata Pelajaran -->
        <div class="form-group">
            <label for="mapel" class="form-label">
                Mata Pelajaran <span class="required-indicator">*</span>
            </label>
            <input type="text" id="mapel" name="mapel" 
                   class="form-control @error('mapel') is-invalid @else @if(old('mapel')) is-valid @endif @enderror" 
                   value="{{ old('mapel', $data->mapel ?? '') }}" placeholder="Masukkan nama mata pelajaran..." required>
            @error('mapel')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Pengajar -->
        <div class="form-group">
            <label for="pengajar" class="form-label">
                Nama Pengajar <span class="required-indicator">*</span>
            </label>
            <input type="text" id="pengajar" name="pengajar" 
                   class="form-control @error('pengajar') is-invalid @else @if(old('pengajar')) is-valid @endif @enderror" 
                   value="{{ old('pengajar', $data->pengajar ?? '') }}" placeholder="Masukkan nama pengajar..." required>
            @error('pengajar')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Aktif / Tidak -->
        <div class="form-group">
            <label for="is_active" class="form-label">
                Status Kelas <span class="required-indicator">*</span>
            </label>
            <select id="is_active" name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                <option value="0" {{ old('is_active', $data->is_active ?? '') == '0' ? 'selected' : '' }}>Kosong / Selesai</option>
                <option value="1" {{ old('is_active', $data->is_active ?? '') == '1' ? 'selected' : '' }}>Sedang Belajar / Aktif</option>
            </select>
            @error('is_active')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- FORM ACTION BUTTONS -->
        <div class="d-flex justify-end gap-3 mt-8 border-t border-neutral-200 pt-5">
            <a href="{{ route('statuskelas.index') }}" class="btn btn-secondary d-flex align-center gap-2" :disabled="loading" style="text-decoration: none;">
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