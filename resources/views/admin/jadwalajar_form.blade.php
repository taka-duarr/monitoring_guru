@extends('layouts.admin')

@section('title', (isset($data) ? 'Edit' : 'Tambah') . ' Jadwal Ajar - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')
<!-- Header Page Title -->
<div class="d-flex align-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-900">{{ isset($data) ? 'Edit' : 'Tambah' }} Jadwal Ajar</h2>
        <p class="text-sm text-neutral-500">Isi detail jadwal mengajar di bawah ini dengan lengkap.</p>
    </div>
    <!-- Back Button -->
    <a href="{{ route('jadwalajar.index') }}" class="btn btn-secondary d-flex align-center gap-2" style="text-decoration: none;">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white" style="padding: 28px; max-w-2xl; margin: 0 auto;">
    <!-- Main Form -->
    <form action="{{ isset($data) ? route('jadwalajar.update', $data->id) : route('jadwalajar.store') }}" method="POST"
          x-data="{ loading: false }"
          @submit="loading = true">
        @csrf
        @if(isset($data)) @method('PUT') @endif

        <!-- Tahun Ajaran -->
        <div class="form-group">
            <label for="tahun_ajaran_id" class="form-label">
                Tahun Ajaran <span class="required-indicator">*</span>
            </label>
            <select id="tahun_ajaran_id" name="tahun_ajaran_id" class="form-select @error('tahun_ajaran_id') is-invalid @enderror" required>
                <option value="">-- Pilih Tahun Ajaran --</option>
                @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}"
                        {{ old('tahun_ajaran_id', $data->tahun_ajaran_id ?? $tahunAjaranAktif?->id) == $ta->id ? 'selected' : '' }}>
                        {{ $ta->name }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
            @error('tahun_ajaran_id')
                <span class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</span>
            @enderror
        </div>

        <!-- Guru Pengajar -->
        <div class="form-group">
            <label for="guru_id" class="form-label">
                Guru Pengajar <span class="required-indicator">*</span>
            </label>
            <select id="guru_id" name="guru_id" class="form-select @error('guru_id') is-invalid @enderror" required>
                <option value="">-- Pilih Guru --</option>
                @foreach($gurus as $g)
                    <option value="{{ $g->id }}" {{ old('guru_id', $data->guru_id ?? '') == $g->id ? 'selected' : '' }}>
                        {{ $g->name }}
                    </option>
                @endforeach
            </select>
            @error('guru_id')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Mata Pelajaran -->
        <div class="form-group">
            <label for="mapel_id" class="form-label">
                Mata Pelajaran <span class="required-indicator">*</span>
            </label>
            <select id="mapel_id" name="mapel_id" class="form-select @error('mapel_id') is-invalid @enderror" required>
                <option value="">-- Pilih Mata Pelajaran --</option>
                @foreach($mapels as $m)
                    <option value="{{ $m->id }}" {{ old('mapel_id', $data->mapel_id ?? '') == $m->id ? 'selected' : '' }}>
                        {{ $m->name }}
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

        <!-- Kelas -->
        <div class="form-group">
            <label for="kelas_id" class="form-label">
                Kelas <span class="required-indicator">*</span>
            </label>
            <select id="kelas_id" name="kelas_id" class="form-select @error('kelas_id') is-invalid @enderror" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}" {{ old('kelas_id', $data->kelas_id ?? '') == $k->id ? 'selected' : '' }}>
                        {{ $k->grade ? $k->grade . ' ' : '' }}{{ $k->name }} {{ $k->angkatan ? '- ' . $k->angkatan->name : '' }}
                    </option>
                @endforeach
            </select>
            @error('kelas_id')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Ruangan -->
        <div class="form-group">
            <label for="ruangan_id" class="form-label">
                Ruangan Belajar <span class="required-indicator">*</span>
            </label>
            <select id="ruangan_id" name="ruangan_id" class="form-select @error('ruangan_id') is-invalid @enderror" required>
                <option value="">-- Pilih Ruangan --</option>
                @foreach($ruangans as $r)
                    <option value="{{ $r->id }}" {{ old('ruangan_id', $data->ruangan_id ?? '') == $r->id ? 'selected' : '' }}>
                        {{ $r->name }}
                    </option>
                @endforeach
            </select>
            @error('ruangan_id')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Hari -->
        <div class="form-group">
            <label for="hari" class="form-label">
                Hari Pelajaran <span class="required-indicator">*</span>
            </label>
            <select id="hari" name="hari" class="form-select @error('hari') is-invalid @enderror" required>
                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                    <option value="{{ $day }}" {{ old('hari', $data->hari ?? '') == $day ? 'selected' : '' }}>
                        {{ $day }}
                    </option>
                @endforeach
            </select>
            @error('hari')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Jam Mulai & Selesai -->
        <div class="grid grid-cols-2 gap-4">
            <div class="form-group">
                <label for="jam_mulai" class="form-label">Jam Mulai <span class="required-indicator">*</span></label>
                <input type="time" id="jam_mulai" name="jam_mulai" 
                       class="form-control @error('jam_mulai') is-invalid @else @if(old('jam_mulai')) is-valid @endif @enderror" 
                       value="{{ old('jam_mulai', $data->jam_mulai ?? '') }}" required>
                @error('jam_mulai')
                    <span class="form-error">
                        <i class="ti ti-alert-circle"></i>
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="jam_selesai" class="form-label">Jam Selesai <span class="required-indicator">*</span></label>
                <input type="time" id="jam_selesai" name="jam_selesai" 
                       class="form-control @error('jam_selesai') is-invalid @else @if(old('jam_selesai')) is-valid @endif @enderror" 
                       value="{{ old('jam_selesai', $data->jam_selesai ?? '') }}" required>
                @error('jam_selesai')
                    <span class="form-error">
                        <i class="ti ti-alert-circle"></i>
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>

        <!-- FORM ACTION BUTTONS -->
        <div class="d-flex justify-end gap-3 mt-8 border-t border-neutral-200 pt-5">
            <a href="{{ route('jadwalajar.index') }}" class="btn btn-secondary d-flex align-center gap-2" :disabled="loading" style="text-decoration: none;">
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