@extends('layouts.admin')

@section('title', 'Buat Laporan - SIMGURU')

@push('styles')
<style>
    /* === LAPORAN PAGE STYLES === */
    .laporan-card {
        background: var(--color-white, #fff);
        border-radius: var(--radius-xl, 16px);
        border: 1px solid var(--color-neutral-200, #e5e7eb);
        box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,.08));
        overflow: hidden;
    }
    .laporan-card-header {
        background: linear-gradient(135deg, var(--color-primary-900, #1B2A4A) 0%, var(--color-primary-700, #243860) 100%);
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .laporan-card-header-icon {
        width: 46px; height: 46px;
        background: rgba(255,255,255,0.15);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; color: #fff;
    }
    .laporan-card-header h3 { color: #fff; font-size: 1rem; font-weight: 700; margin: 0; }
    .laporan-card-header p  { color: rgba(255,255,255,0.7); font-size: 0.78rem; margin: 2px 0 0; }
    .laporan-card-body { padding: 28px 24px; }

    /* === JENIS LAPORAN RADIO CARDS === */
    .jenis-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
        margin-bottom: 28px;
    }
    .jenis-card {
        position: relative;
        cursor: pointer;
    }
    .jenis-card input[type="radio"] {
        position: absolute; opacity: 0; width: 0; height: 0;
    }
    .jenis-card-label {
        display: flex; flex-direction: column; align-items: flex-start;
        padding: 16px 18px;
        border: 2px solid var(--color-neutral-200, #e5e7eb);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: var(--color-neutral-50, #f9fafb);
        gap: 8px;
        user-select: none;
    }
    .jenis-card-label:hover {
        border-color: var(--color-primary-400, #6b89c4);
        background: var(--color-primary-50, #eef2fb);
    }
    .jenis-card input[type="radio"]:checked + .jenis-card-label {
        border-color: var(--color-primary-700, #243860);
        background: var(--color-primary-50, #eef2fb);
        box-shadow: 0 0 0 3px rgba(27,42,74,0.10);
    }
    .jenis-card-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; flex-shrink: 0;
    }
    .jenis-card-title { font-size: 0.85rem; font-weight: 700; color: var(--color-primary-900, #1B2A4A); }
    .jenis-card-desc  { font-size: 0.75rem; color: var(--color-neutral-500, #6b7280); line-height: 1.4; }
    .jenis-card-check {
        position: absolute; top: 10px; right: 10px;
        width: 20px; height: 20px;
        border-radius: 50%;
        background: var(--color-primary-700, #243860);
        color: white;
        display: none; align-items: center; justify-content: center;
        font-size: 11px;
    }
    .jenis-card input[type="radio"]:checked ~ .jenis-card-check { display: flex; }

    /* === SECTION DIVIDER === */
    .section-divider {
        border: none; border-top: 1px solid var(--color-neutral-200, #e5e7eb);
        margin: 24px 0;
    }
    .section-title {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--color-neutral-500, #6b7280);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 14px;
    }

    /* === FORMAT BUTTONS === */
    .format-toggle {
        display: flex; gap: 10px;
    }
    .format-option { position: relative; }
    .format-option input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
    .format-option-label {
        display: flex; align-items: center; gap: 8px;
        padding: 10px 20px;
        border: 2px solid var(--color-neutral-200, #e5e7eb);
        border-radius: 10px;
        cursor: pointer;
        font-size: 0.85rem; font-weight: 600;
        transition: all 0.2s;
        background: var(--color-neutral-50, #f9fafb);
    }
    .format-option-label:hover { border-color: var(--color-primary-400, #6b89c4); }
    .format-option input[type="radio"]:checked + .format-option-label {
        border-color: var(--color-primary-700, #243860);
        background: var(--color-primary-50, #eef2fb);
        color: var(--color-primary-900, #1B2A4A);
    }
    .format-pdf-label   { color: #dc2626; }
    .format-excel-label { color: #16a34a; }
    .format-option input[type="radio"]:checked + .format-pdf-label   { color: #991b1b; }
    .format-option input[type="radio"]:checked + .format-excel-label { color: #166534; }

    /* === GENERATE BUTTON === */
    .btn-generate {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 13px 32px;
        background: linear-gradient(135deg, #1B2A4A, #2d4a8a);
        color: white;
        border: none; border-radius: 10px;
        font-size: 0.9rem; font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(27,42,74,0.25);
    }
    .btn-generate:hover {
        background: linear-gradient(135deg, #243860, #3a5aa0);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(27,42,74,0.35);
    }
    .btn-generate:disabled { opacity: 0.65; cursor: not-allowed; transform: none; }

    /* === FILTER PANEL === */
    .filter-panel {
        background: var(--color-neutral-50, #f9fafb);
        border: 1px solid var(--color-neutral-200, #e5e7eb);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
    }
    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
    }

    /* === SELECT OPTION FIX (pastikan teks terlihat di semua browser) === */
    .laporan-card-body select.form-control,
    .laporan-card-body select.form-control option {
        color: #1a202c !important;
        background-color: #ffffff !important;
    }
    .laporan-card-body select.form-control option:disabled {
        color: #9ca3af !important;
    }
</style>
@endpush

@section('content')
<div x-data="{
    jenis: 'rekap_kehadiran',
    format: 'pdf',
    loading: false
}">
    {{-- Header --}}
    <div class="d-flex align-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Buat Laporan</h2>
            <p class="text-sm text-neutral-500">Generate laporan kehadiran guru dalam format PDF atau Excel</p>
        </div>
        <a href="{{ route('laporan.riwayat') }}" class="btn btn-outline d-flex align-center gap-2">
            <i class="ti ti-history"></i> Riwayat Laporan
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-4" style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:10px;color:#065f46;">
        <i class="ti ti-circle-check" style="font-size:18px;"></i>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-error mb-4" style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:10px;color:#991b1b;">
        <i class="ti ti-alert-circle" style="font-size:18px;"></i>
        {{ session('error') }}
    </div>
    @endif

    <form method="POST" action="{{ route('laporan.generate') }}" @submit="setTimeout(() => { loading = true; }, 100); setTimeout(() => { loading = false; }, 5000);">
        @csrf

        {{-- ============================================================ --}}
        {{-- STEP 1: PILIH JENIS LAPORAN --}}
        {{-- ============================================================ --}}
        <div class="laporan-card mb-4">
            <div class="laporan-card-header">
                <div class="laporan-card-header-icon"><i class="ti ti-list-check"></i></div>
                <div>
                    <h3>Langkah 1 — Pilih Jenis Laporan</h3>
                    <p>Pilih kategori data yang ingin dijadikan laporan</p>
                </div>
            </div>
            <div class="laporan-card-body">
                <div class="jenis-grid">

                    {{-- Rekap Kehadiran --}}
                    <label class="jenis-card">
                        <input type="radio" name="jenis_laporan" value="rekap_kehadiran"
                               x-model="jenis" checked>
                        <div class="jenis-card-label">
                            <div class="jenis-card-icon" style="background:#eef2fb;">
                                <i class="ti ti-users" style="color:#1B2A4A;"></i>
                            </div>
                            <div>
                                <div class="jenis-card-title">Rekap Kehadiran</div>
                                <div class="jenis-card-desc">Rekapitulasi absen masuk & keluar guru per periode</div>
                            </div>
                        </div>
                        <div class="jenis-card-check"><i class="ti ti-check"></i></div>
                    </label>

                    {{-- Perizinan --}}
                    <label class="jenis-card">
                        <input type="radio" name="jenis_laporan" value="perizinan"
                               x-model="jenis">
                        <div class="jenis-card-label">
                            <div class="jenis-card-icon" style="background:#fef3c7;">
                                <i class="ti ti-file-check" style="color:#92400e;"></i>
                            </div>
                            <div>
                                <div class="jenis-card-title">Perizinan Guru</div>
                                <div class="jenis-card-desc">Daftar izin sakit, dinas luar, dan lainnya</div>
                            </div>
                        </div>
                        <div class="jenis-card-check"><i class="ti ti-check"></i></div>
                    </label>

                    {{-- Kelas Kosong --}}
                    <label class="jenis-card">
                        <input type="radio" name="jenis_laporan" value="kelas_kosong"
                               x-model="jenis">
                        <div class="jenis-card-label">
                            <div class="jenis-card-icon" style="background:#fee2e2;">
                                <i class="ti ti-school" style="color:#991b1b;"></i>
                            </div>
                            <div>
                                <div class="jenis-card-title">Kelas Kosong</div>
                                <div class="jenis-card-desc">Rekap kelas yang tidak terlaksana / kosong</div>
                            </div>
                        </div>
                        <div class="jenis-card-check"><i class="ti ti-check"></i></div>
                    </label>

                    {{-- Jadwal Mengajar --}}
                    <label class="jenis-card">
                        <input type="radio" name="jenis_laporan" value="jadwal_ajar"
                               x-model="jenis">
                        <div class="jenis-card-label">
                            <div class="jenis-card-icon" style="background:#d1fae5;">
                                <i class="ti ti-calendar" style="color:#065f46;"></i>
                            </div>
                            <div>
                                <div class="jenis-card-title">Jadwal Mengajar</div>
                                <div class="jenis-card-desc">Daftar jadwal mengajar seluruh guru</div>
                            </div>
                        </div>
                        <div class="jenis-card-check"><i class="ti ti-check"></i></div>
                    </label>

                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- STEP 2: FILTER DATA --}}
        {{-- ============================================================ --}}
        <div class="laporan-card mb-4">
            <div class="laporan-card-header">
                <div class="laporan-card-header-icon"><i class="ti ti-filter"></i></div>
                <div>
                    <h3>Langkah 2 — Tentukan Filter Data</h3>
                    <p>Pilih rentang tanggal dan filter tambahan sesuai kebutuhan</p>
                </div>
            </div>
            <div class="laporan-card-body">

                {{-- === Filter Periode (untuk semua kecuali jadwal_ajar) === --}}
                <div x-show="jenis !== 'jadwal_ajar'" x-transition>
                    <p class="section-title">Rentang Tanggal</p>
                    <div class="filter-row mb-4">
                        <div class="form-group">
                            <label class="form-label" for="tanggal_mulai">Tanggal Mulai</label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                                   class="form-control"
                                   value="{{ old('tanggal_mulai', now()->startOfMonth()->format('Y-m-d')) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="tanggal_akhir">Tanggal Akhir</label>
                            <input type="date" id="tanggal_akhir" name="tanggal_akhir"
                                   class="form-control"
                                   value="{{ old('tanggal_akhir', now()->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>

                <hr class="section-divider" x-show="jenis !== 'jadwal_ajar'">

                {{-- === Filter: Rekap Kehadiran === --}}
                <div x-show="jenis === 'rekap_kehadiran'" x-transition>
                    <p class="section-title">Filter Kehadiran</p>
                    <div class="filter-row">
                        <div class="form-group">
                            <label class="form-label" for="guru_id_kehadiran">Guru (opsional)</label>
                            <select name="guru_id" id="guru_id_kehadiran" class="form-control" style="color:#111827;background-color:#ffffff;">
                                <option value="" style="color:#111827;background:#ffffff;">Semua Guru</option>
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}" style="color:#111827;background:#ffffff;">{{ $guru->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="kelas_id_kehadiran">Kelas (opsional)</label>
                            <select name="kelas_id" id="kelas_id_kehadiran" class="form-control" style="color:#111827;background-color:#ffffff;">
                                <option value="" style="color:#111827;background:#ffffff;">Semua Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" style="color:#111827;background:#ffffff;">{{ $k->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- === Filter: Perizinan === --}}
                <div x-show="jenis === 'perizinan'" x-transition>
                    <p class="section-title">Filter Perizinan</p>
                    <div class="filter-row">
                        <div class="form-group">
                            <label class="form-label" for="jenis_izin">Jenis Izin (opsional)</label>
                            <select name="jenis_izin" id="jenis_izin" class="form-control" style="color:#111827;background-color:#ffffff;">
                                <option value="" style="color:#111827;background:#ffffff;">Semua Jenis Izin</option>
                                <option value="sakit" style="color:#111827;background:#ffffff;">Sakit</option>
                                <option value="izin" style="color:#111827;background:#ffffff;">Izin</option>
                                <option value="dinas" style="color:#111827;background:#ffffff;">Dinas Luar</option>
                                <option value="lainnya" style="color:#111827;background:#ffffff;">Lainnya</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- === Filter: Kelas Kosong === --}}
                <div x-show="jenis === 'kelas_kosong'" x-transition>
                    <p class="section-title">Filter Kelas Kosong</p>
                    <div class="filter-row">
                        <div class="form-group">
                            <label class="form-label" for="kelas_id_kosong">Kelas (opsional)</label>
                            <select name="kelas_id" id="kelas_id_kosong" class="form-control" style="color:#111827;background-color:#ffffff;">
                                <option value="" style="color:#111827;background:#ffffff;">Semua Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" style="color:#111827;background:#ffffff;">{{ $k->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- === Filter: Jadwal Ajar === --}}
                <div x-show="jenis === 'jadwal_ajar'" x-transition>
                    <p class="section-title">Filter Jadwal Mengajar</p>
                    <div class="filter-row">
                        <div class="form-group">
                            <label class="form-label" for="guru_id_jadwal">Guru (opsional)</label>
                            <select name="guru_id" id="guru_id_jadwal" class="form-control" style="color:#111827;background-color:#ffffff;">
                                <option value="" style="color:#111827;background:#ffffff;">Semua Guru</option>
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}" style="color:#111827;background:#ffffff;">{{ $guru->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="kelas_id_jadwal">Kelas (opsional)</label>
                            <select name="kelas_id" id="kelas_id_jadwal" class="form-control" style="color:#111827;background-color:#ffffff;">
                                <option value="" style="color:#111827;background:#ffffff;">Semua Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" style="color:#111827;background:#ffffff;">{{ $k->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="mapel_id_jadwal">Mata Pelajaran (opsional)</label>
                            <select name="mapel_id" id="mapel_id_jadwal" class="form-control" style="color:#111827;background-color:#ffffff;">
                                <option value="" style="color:#111827;background:#ffffff;">Semua Mapel</option>
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->id }}" style="color:#111827;background:#ffffff;">{{ $mapel->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- STEP 3: FORMAT & GENERATE --}}
        {{-- ============================================================ --}}
        <div class="laporan-card mb-4">
            <div class="laporan-card-header">
                <div class="laporan-card-header-icon"><i class="ti ti-download"></i></div>
                <div>
                    <h3>Langkah 3 — Pilih Format & Generate</h3>
                    <p>Pilih format output dan mulai buat laporan</p>
                </div>
            </div>
            <div class="laporan-card-body">
                <p class="section-title">Format Output</p>
                <div class="format-toggle mb-4">
                    <div class="format-option">
                        <input type="radio" name="format" id="format_pdf" value="pdf" x-model="format" checked>
                        <label for="format_pdf" class="format-option-label format-pdf-label">
                            <i class="ti ti-file-type-pdf" style="font-size:20px;"></i>
                            <span>PDF</span>
                        </label>
                    </div>
                    <div class="format-option">
                        <input type="radio" name="format" id="format_excel" value="excel" x-model="format">
                        <label for="format_excel" class="format-option-label format-excel-label">
                            <i class="ti ti-file-type-xls" style="font-size:20px;"></i>
                            <span>Excel (.xlsx)</span>
                        </label>
                    </div>
                </div>

                <hr class="section-divider">

                {{-- Preview info --}}
                <div style="background:var(--color-neutral-50,#f9fafb);border-radius:10px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:12px;border:1px solid var(--color-neutral-200,#e5e7eb);">
                    <i class="ti ti-info-circle" style="color:#1B2A4A;font-size:20px;flex-shrink:0;"></i>
                    <div style="font-size:0.82rem;color:var(--color-neutral-600,#4b5563);">
                        Laporan akan otomatis ter-<strong>download</strong> dan tersimpan di
                        <a href="{{ route('laporan.riwayat') }}" style="color:#1B2A4A;font-weight:600;text-decoration:underline;">Riwayat Laporan</a>
                        untuk dapat diunduh kembali kapan saja.
                    </div>
                </div>

                <button type="submit" class="btn-generate" :disabled="loading">
                    <template x-if="!loading">
                        <span style="display:flex;align-items:center;gap:8px;">
                            <i class="ti ti-file-report" style="font-size:18px;"></i>
                            Generate &amp; Download
                        </span>
                    </template>
                    <template x-if="loading">
                        <span style="display:flex;align-items:center;gap:8px;">
                            <i class="ti ti-loader-2" style="font-size:18px;animation:spin 1s linear infinite;"></i>
                            Sedang membuat laporan...
                        </span>
                    </template>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endpush

@endsection
