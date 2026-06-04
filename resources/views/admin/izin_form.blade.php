@extends('layouts.admin')

@section('title', (isset($data) ? 'Edit' : 'Tambah') . ' Pengajuan Izin - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')
<!-- Header Page Title -->
<div class="d-flex align-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-900">{{ isset($data) ? 'Edit' : 'Tambah' }} Pengajuan Izin</h2>
        <p class="text-sm text-neutral-500">Sesuaikan data pengajuan izin tidak mengajar guru di bawah ini.</p>
    </div>
    <!-- Back Button -->
    <a href="{{ route('izin.index') }}" class="btn btn-secondary d-flex align-center gap-2" style="text-decoration: none;">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white" style="padding: 28px; max-w-2xl; margin: 0 auto;">
    <!-- Main Form -->
    <form action="{{ isset($data) ? route('izin.update', $data->id) : route('izin.store') }}" method="POST"
          x-data="{ loading: false }"
          @submit="loading = true">
        @csrf
        @if(isset($data)) @method('PUT') @endif

        <!-- Guru -->
        <div class="form-group">
            <label for="guru_id" class="form-label">
                Pilih Guru <span class="required-indicator">*</span>
            </label>
            <select id="guru_id" name="guru_id" class="form-select @error('guru_id') is-invalid @enderror" style="color: #111827; background: #ffffff;" required>
                <option value="">-- Pilih Guru --</option>
                @foreach($gurus as $g)
                    <option value="{{ $g->id }}" {{ old('guru_id', $data->guru_id ?? '') == $g->id ? 'selected' : '' }}>
                        {{ $g->name }} ({{ $g->nik }})
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

        <!-- Jadwal Ajar -->
        <div class="form-group">
            <label for="jadwal_ajar_id" class="form-label">
                Pilih Jadwal Ajar (opsional)
            </label>
            <select id="jadwal_ajar_id" name="jadwal_ajar_id" class="form-select @error('jadwal_ajar_id') is-invalid @enderror" style="color: #111827; background: #ffffff;">
                <option value="">-- Izin Umum (Tidak Terikat Jadwal Spesifik) --</option>
                @foreach($jadwals as $j)
                    <option value="{{ $j->id }}" data-guru-id="{{ $j->guru_id }}" {{ old('jadwal_ajar_id', $data->jadwal_ajar_id ?? '') == $j->id ? 'selected' : '' }}>
                        {{ $j->hari }} · {{ $j->jam_mulai }}-{{ $j->jam_selesai }} · {{ $j->mapel->name ?? '-' }} ({{ $j->kelas->name ?? '-' }})
                    </option>
                @endforeach
            </select>
            @error('jadwal_ajar_id')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Tanggal -->
        <div class="form-group">
            <label for="tanggal_izin" class="form-label">
                Tanggal Izin <span class="required-indicator">*</span>
            </label>
            <input type="date" id="tanggal_izin" name="tanggal_izin" 
                   class="form-control @error('tanggal_izin') is-invalid @else @if(old('tanggal_izin')) is-valid @endif @enderror" 
                   value="{{ old('tanggal_izin', $data->tanggal_izin ?? '') }}" required>
            @error('tanggal_izin')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Jam Izin -->
        <div class="form-group">
            <label for="jam_izin" class="form-label">
                Jam Mulai Izin <span class="required-indicator">*</span>
            </label>
            <input type="time" id="jam_izin" name="jam_izin" 
                   class="form-control @error('jam_izin') is-invalid @enderror" 
                   value="{{ old('jam_izin', $data->jam_izin ?? '07:00') }}" required>
            <span class="form-helper">Default: 07:00 (Jam masuk sekolah standar).</span>
            @error('jam_izin')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Judul -->
        <div class="form-group">
            <label for="judul" class="form-label">
                Judul Izin <span class="required-indicator">*</span>
            </label>
            <input type="text" id="judul" name="judul" 
                   class="form-control @error('judul') is-invalid @else @if(old('judul')) is-valid @endif @enderror" 
                   value="{{ old('judul', $data->judul ?? '') }}" placeholder="Contoh: Sakit Flu, Izin Acara Keluarga..." required>
            @error('judul')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Pesan -->
        <div class="form-group">
            <label for="pesan" class="form-label">
                Pesan / Keterangan <span class="required-indicator">*</span>
            </label>
            <textarea id="pesan" name="pesan" rows="4" 
                      class="form-control @error('pesan') is-invalid @enderror" placeholder="Tuliskan keterangan detail alasan izin..." required>{{ old('pesan', $data->pesan ?? '') }}</textarea>
            @error('pesan')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Approval / Status Persetujuan -->
        <div class="form-group">
            <label for="approval" class="form-label">
                Persetujuan <span class="required-indicator">*</span>
            </label>
            <select id="approval" name="approval" class="form-select @error('approval') is-invalid @enderror" required>
                <option value="0" {{ old('approval', $data->approval ?? '') == '0' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="1" {{ old('approval', $data->approval ?? '') == '1' ? 'selected' : '' }}>Disetujui / Diizinkan</option>
            </select>
            @error('approval')
                <span class="form-error">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </span>
            @enderror
        </div>

        <!-- Bukti File Lampiran (jika ada) -->
        @if(isset($data) && $data->file)
            <div class="form-group">
                <label class="form-label">Bukti File Lampiran</label>
                <div class="d-flex align-center">
                    <a href="{{ asset('storage/' . $data->file) }}" target="_blank" class="btn btn-secondary d-inline-flex align-center gap-2" style="text-decoration: none;">
                        <i class="ti ti-download text-primary"></i> Unduh / Lihat Dokumen Lampiran
                    </a>
                </div>
            </div>
        @endif

        <!-- FORM ACTION BUTTONS -->
        <div class="d-flex justify-end gap-3 mt-8 border-t border-neutral-200 pt-5">
            <a href="{{ route('izin.index') }}" class="btn btn-secondary d-flex align-center gap-2" :disabled="loading" style="text-decoration: none;">
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

@push('scripts')
<script>
    function filterJadwals() {
        const guruId = document.getElementById('guru_id').value;
        const jadwalSelect = document.getElementById('jadwal_ajar_id');
        if (!jadwalSelect) return;
        const options = jadwalSelect.options;
        for (let i = 0; i < options.length; i++) {
            const option = options[i];
            const optGuruId = option.getAttribute('data-guru-id');
            if (!optGuruId) continue;
            if (optGuruId === guruId) {
                option.style.display = '';
                option.disabled = false;
            } else {
                option.style.display = 'none';
                option.disabled = true;
                if (option.selected) {
                    option.selected = false;
                    jadwalSelect.value = '';
                }
            }
        }
    }
    document.getElementById('guru_id').addEventListener('change', filterJadwals);
    document.addEventListener('DOMContentLoaded', filterJadwals);
    // Jalankan sekali saat load (untuk edit/old inputs)
    setTimeout(filterJadwals, 100);
</script>
@endpush
@endsection