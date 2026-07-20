@extends('layouts.admin')

@section('title', (isset($data) ? 'Edit' : 'Tambah') . ' Kehadiran Masuk - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')
<!-- Header Page Title -->
<div class="d-flex align-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-900">{{ isset($data) ? 'Edit' : 'Tambah' }} Kehadiran Masuk</h2>
        <p class="text-sm text-neutral-500">Sesuaikan data log kehadiran masuk guru di bawah ini.</p>
    </div>
    <!-- Back Button -->
    <a href="{{ route('absenmasuk.index') }}" class="btn btn-secondary d-flex align-center gap-2" style="text-decoration: none;">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white" style="padding: 28px; max-w-2xl; margin: 0 auto;">
    <!-- Main Form -->
    <form action="{{ isset($data) ? route('absenmasuk.update', $data->id) : route('absenmasuk.store') }}" method="POST"
          x-data="{ loading: false }"
          @submit="loading = true">
        @csrf
        @if(isset($data)) @method('PUT') @endif

        <!-- Tanggal -->
        <div class="form-group">
            <label for="tanggal" class="form-label">
                Tanggal Kehadiran <span class="required-indicator">*</span>
            </label>
            <input type="date" id="tanggal" name="tanggal" 
                   class="form-control @error('tanggal') is-invalid @else @if(old('tanggal')) is-valid @endif @enderror" 
                   value="{{ old('tanggal', $data->tanggal ?? '') }}" required>
            @error('tanggal')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Jam Masuk -->
        <div class="form-group">
            <label for="jam_masuk" class="form-label">
                Jam Masuk <span class="required-indicator">*</span>
            </label>
            <input type="time" id="jam_masuk" name="jam_masuk" 
                   class="form-control @error('jam_masuk') is-invalid @else @if(old('jam_masuk')) is-valid @endif @enderror" 
                   value="{{ old('jam_masuk', $data->jam_masuk ?? '') }}" required>
            @error('jam_masuk')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        @if(isset($data))
        <!-- Jam Keluar (Edit Only) -->
        <div class="form-group mt-4">
            <label for="jam_keluar" class="form-label">
                Jam Keluar
            </label>
            <input type="time" id="jam_keluar" name="jam_keluar" 
                   class="form-control @error('jam_keluar') is-invalid @else @if(old('jam_keluar')) is-valid @endif @enderror" 
                   value="{{ old('jam_keluar', $data->absenKeluar->jam_keluar ?? '') }}">
            <span class="text-xs text-neutral-500 mt-1 d-block">Kosongkan jika guru belum melakukan absensi keluar (checkout).</span>
            @error('jam_keluar')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>
        @endif

        <!-- FORM ACTION BUTTONS -->
        <div class="d-flex justify-end gap-3 mt-8 border-t border-neutral-200 pt-5">
            <a href="{{ route('absenmasuk.index') }}" class="btn btn-secondary d-flex align-center gap-2" :disabled="loading" style="text-decoration: none;">
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