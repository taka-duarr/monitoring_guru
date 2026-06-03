@extends('layouts.admin')

@section('title', (isset($data) ? 'Edit' : 'Tambah') . ' Kehadiran Keluar - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')
<!-- Header Page Title -->
<div class="d-flex align-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-900">{{ isset($data) ? 'Edit' : 'Tambah' }} Kehadiran Keluar</h2>
        <p class="text-sm text-neutral-500">Sesuaikan data log keluar mengajar guru di bawah ini.</p>
    </div>
    <!-- Back Button -->
    <a href="{{ route('absenkeluar.index') }}" class="btn btn-secondary d-flex align-center gap-2" style="text-decoration: none;">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white" style="padding: 28px; max-w-2xl; margin: 0 auto;">
    <!-- Main Form -->
    <form action="{{ isset($data) ? route('absenkeluar.update', $data->id) : route('absenkeluar.store') }}" method="POST"
          x-data="{ loading: false }"
          @submit="loading = true">
        @csrf
        @if(isset($data)) @method('PUT') @endif

        <!-- Sesi Absen Masuk -->
        <div class="form-group">
            <label for="absen_masuk_id" class="form-label">
                Pilih Sesi Kehadiran Masuk <span class="required-indicator">*</span>
            </label>
            <select id="absen_masuk_id" name="absen_masuk_id" class="form-select @error('absen_masuk_id') is-invalid @enderror" required>
                <option value="">-- Pilih Sesi Kehadiran --</option>
                @foreach($absenMasuks as $rel)
                    <option value="{{ $rel->id }}" {{ old('absen_masuk_id', $data->absen_masuk_id ?? '') == $rel->id ? 'selected' : '' }}>
                        Tanggal: {{ $rel->tanggal }} – Guru: {{ $rel->guru->name ?? '-' }} (Kelas: {{ $rel->kelas->name ?? '-' }})
                    </option>
                @endforeach
            </select>
            @error('absen_masuk_id')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Jam Keluar -->
        <div class="form-group">
            <label for="jam_keluar" class="form-label">
                Waktu Keluar <span class="required-indicator">*</span>
            </label>
            <input type="time" id="jam_keluar" name="jam_keluar" 
                   class="form-control @error('jam_keluar') is-invalid @else @if(old('jam_keluar')) is-valid @endif @enderror" 
                   value="{{ old('jam_keluar', $data->jam_keluar ?? '') }}" required>
            @error('jam_keluar')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="status" class="form-label">
                Status Keluar <span class="required-indicator">*</span>
            </label>
            <input type="text" id="status" name="status" 
                   class="form-control @error('status') is-invalid @else @if(old('status')) is-valid @endif @enderror" 
                   value="{{ old('status', $data->status ?? 'Selesai') }}" placeholder="Contoh: Selesai, Meninggalkan Kelas..." required>
            @error('status')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- FORM ACTION BUTTONS -->
        <div class="d-flex justify-end gap-3 mt-8 border-t border-neutral-200 pt-5">
            <a href="{{ route('absenkeluar.index') }}" class="btn btn-secondary d-flex align-center gap-2" :disabled="loading" style="text-decoration: none;">
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