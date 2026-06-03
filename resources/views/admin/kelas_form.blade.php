@extends('layouts.admin')

@section('title', (isset($data) ? 'Edit' : 'Tambah') . ' Kelas - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')
<!-- Header Page Title -->
<div class="d-flex align-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-900">{{ isset($data) ? 'Edit' : 'Tambah' }} Kelas</h2>
        <p class="text-sm text-neutral-500">Isi detail data rombel/kelas di bawah ini.</p>
    </div>
    <!-- Back Button -->
    <a href="{{ route('kelas.index') }}" class="btn btn-secondary d-flex align-center gap-2" style="text-decoration: none;">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white" style="padding: 28px; max-w-2xl; margin: 0 auto;">
    <!-- Main Form -->
    <form action="{{ isset($data) ? route('kelas.update', $data->id) : route('kelas.store') }}" method="POST"
          x-data="{ loading: false }"
          @submit="loading = true">
        @csrf
        @if(isset($data)) @method('PUT') @endif

        <!-- Nama Kelas -->
        <div class="form-group">
            <label for="name" class="form-label">
                Nama Kelas <span class="required-indicator">*</span>
            </label>
            <input type="text" id="name" name="name"
                   class="form-control @error('name') is-invalid @else @if(old('name')) is-valid @endif @enderror"
                   value="{{ old('name', $data->name ?? '') }}" placeholder="Contoh: XII IPA 1, X IPS 3..." required>
            @error('name')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Angkatan -->
        <div class="form-group">
            <label for="angkatan_id" class="form-label">
                Angkatan <span class="required-indicator">*</span>
            </label>
            <select id="angkatan_id" name="angkatan_id" class="form-select @error('angkatan_id') is-invalid @enderror" required>
                <option value="">-- Pilih Angkatan --</option>
                @foreach($angkatans as $ang)
                    <option value="{{ $ang->id }}" {{ old('angkatan_id', $data->angkatan_id ?? '') == $ang->id ? 'selected' : '' }}>
                        {{ $ang->name }}
                    </option>
                @endforeach
            </select>
            @error('angkatan_id')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Grade / Tingkatan -->
        <div class="form-group">
            <label for="grade" class="form-label">
                Tingkatan Kelas <span class="required-indicator">*</span>
            </label>
            <select id="grade" name="grade" class="form-select @error('grade') is-invalid @enderror" required>
                <option value="">-- Pilih Kelas --</option>
                <option value="10" {{ old('grade', $data->grade ?? '') == '10' ? 'selected' : '' }}>Kelas 10</option>
                <option value="11" {{ old('grade', $data->grade ?? '') == '11' ? 'selected' : '' }}>Kelas 11</option>
                <option value="12" {{ old('grade', $data->grade ?? '') == '12' ? 'selected' : '' }}>Kelas 12</option>
            </select>
            @error('grade')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Jurusan -->
        <div class="form-group">
            <label for="jurusan_id" class="form-label">
                Jurusan <span class="required-indicator">*</span>
            </label>
            <select id="jurusan_id" name="jurusan_id" class="form-select @error('jurusan_id') is-invalid @enderror" required>
                <option value="">-- Pilih Jurusan --</option>
                @foreach($jurusans as $rel)
                    <option value="{{ $rel->id }}" {{ old('jurusan_id', $data->jurusan_id ?? '') == $rel->id ? 'selected' : '' }}>
                        {{ $rel->name }} ({{ $rel->kode_jurusan }})
                    </option>
                @endforeach
            </select>
            @error('jurusan_id')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- FORM ACTION BUTTONS -->
        <div class="d-flex justify-end gap-3 mt-8 border-t border-neutral-200 pt-5">
            <a href="{{ route('kelas.index') }}" class="btn btn-secondary d-flex align-center gap-2" :disabled="loading" style="text-decoration: none;">
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
