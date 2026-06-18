@extends('layouts.admin')

@section('title', (isset($data) ? 'Edit' : 'Tambah') . ' Ruangan - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')
<!-- Header Page Title -->
<div class="d-flex align-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-900">{{ isset($data) ? 'Edit' : 'Tambah' }} Ruangan</h2>
        <p class="text-sm text-neutral-500">Sesuaikan informasi detail ruangan di bawah ini.</p>
    </div>
    <!-- Back Button -->
    <a href="{{ route('ruangan.index') }}" class="btn btn-secondary d-flex align-center gap-2" style="text-decoration: none;">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white" style="padding: 28px; max-w-2xl; margin: 0 auto;">
    <!-- Main Form -->
    <form action="{{ isset($data) ? route('ruangan.update', $data->id) : route('ruangan.store') }}" method="POST"
          x-data="{ loading: false }"
          @submit="loading = true">
        @csrf
        @if(isset($data)) @method('PUT') @endif

        <!-- Nama Ruangan -->
        <div class="form-group mb-4">
            <label for="name" class="form-label">
                Nama / Nomor Ruangan <span class="required-indicator">*</span>
            </label>
            <input type="text" id="name" name="name" 
                   class="form-control @error('name') is-invalid @else @if(old('name')) is-valid @endif @enderror" 
                   value="{{ old('name', $data->name ?? '') }}" placeholder="Contoh: R. Teori 12, Lab Komputer A..." required>
            @error('name')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Lantai -->
        <div class="form-group">
            <label for="lantai" class="form-label">
                Lokasi Lantai <span class="text-neutral-400 font-normal">(Opsional)</span>
            </label>
            <select id="lantai" name="lantai" class="form-control @error('lantai') is-invalid @enderror">
                <option value="">-- Pilih Lantai --</option>
                @for($i = 1; $i <= 6; $i++)
                    <option value="{{ $i }}" {{ old('lantai', $data->lantai ?? '') == $i ? 'selected' : '' }}>Lantai {{ $i }}</option>
                @endfor
            </select>
            @error('lantai')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- FORM ACTION BUTTONS -->
        <div class="d-flex justify-end gap-3 mt-8 border-t border-neutral-200 pt-5">
            <a href="{{ route('ruangan.index') }}" class="btn btn-secondary d-flex align-center gap-2" :disabled="loading" style="text-decoration: none;">
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