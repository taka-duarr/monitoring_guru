@extends('layouts.admin')

@section('title', (isset($data) ? 'Edit' : 'Tambah') . ' Jurusan - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')
<!-- Header Page Title -->
<div class="d-flex align-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-900">{{ isset($data) ? 'Edit' : 'Tambah' }} Jurusan</h2>
        <p class="text-sm text-neutral-500">Sesuaikan informasi detail rumpun jurusan di bawah ini.</p>
    </div>
    <!-- Back Button -->
    <a href="{{ route('jurusan.index') }}" class="btn btn-secondary d-flex align-center gap-2" style="text-decoration: none;">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white" style="padding: 28px; max-w-2xl; margin: 0 auto;">
    <!-- Main Form -->
    <form action="{{ isset($data) ? route('jurusan.update', $data->id) : route('jurusan.store') }}" method="POST"
          x-data="{ loading: false }"
          @submit="loading = true">
        @csrf
        @if(isset($data)) @method('PUT') @endif

        <!-- Nama Jurusan -->
        <div class="form-group">
            <label for="name" class="form-label">
                Nama Jurusan <span class="required-indicator">*</span>
            </label>
            <input type="text" id="name" name="name" 
                   class="form-control @error('name') is-invalid @else @if(old('name')) is-valid @endif @enderror" 
                   value="{{ old('name', $data->name ?? '') }}" placeholder="Contoh: Rekayasa Perangkat Lunak, Teknik Kendaraan Ringan..." required>
            @error('name')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Kode Jurusan -->
        <div class="form-group">
            <label for="kode_jurusan" class="form-label">
                Kode Singkat Jurusan <span class="required-indicator">*</span>
            </label>
            <input type="text" id="kode_jurusan" name="kode_jurusan" 
                   class="form-control @error('kode_jurusan') is-invalid @else @if(old('kode_jurusan')) is-valid @endif @enderror" 
                   value="{{ old('kode_jurusan', $data->kode_jurusan ?? '') }}" placeholder="Contoh: RPL, TKR, Akuntansi..." required>
            @error('kode_jurusan')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- FORM ACTION BUTTONS -->
        <div class="d-flex justify-end gap-3 mt-8 border-t border-neutral-200 pt-5">
            <a href="{{ route('jurusan.index') }}" class="btn btn-secondary d-flex align-center gap-2" :disabled="loading" style="text-decoration: none;">
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