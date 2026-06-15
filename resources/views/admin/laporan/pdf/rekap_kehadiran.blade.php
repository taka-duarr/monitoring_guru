<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Rekap Kehadiran Guru</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a202c; background: #fff; }

    /* === KOP SURAT === */
    .kop { display: flex; align-items: center; border-bottom: 3px solid #1B2A4A; padding-bottom: 10px; margin-bottom: 12px; }
    .kop-logo { width: 60px; height: 60px; background: #1B2A4A; border-radius: 8px;
                display: flex; align-items: center; justify-content: center;
                color: white; font-size: 20px; font-weight: 700; flex-shrink: 0; text-align: center; line-height: 60px; }
    .kop-text { margin-left: 14px; }
    .kop-sekolah { font-size: 14px; font-weight: 700; color: #1B2A4A; }
    .kop-alamat { font-size: 9px; color: #555; margin-top: 2px; }

    /* === JUDUL LAPORAN === */
    .laporan-judul { text-align: center; margin: 14px 0 6px; }
    .laporan-judul h2 { font-size: 13px; font-weight: 700; color: #1B2A4A; text-transform: uppercase; letter-spacing: 0.5px; }
    .laporan-judul p { font-size: 9px; color: #555; margin-top: 3px; }

    /* === INFO FILTER === */
    .info-filter { display: flex; gap: 20px; margin-bottom: 10px; padding: 7px 10px;
                   background: #f0f4fb; border-left: 3px solid #1B2A4A; border-radius: 3px; }
    .info-item { font-size: 9px; color: #555; }
    .info-item span { font-weight: 600; color: #1B2A4A; }

    /* === TABEL === */
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    thead th { background: #1B2A4A; color: #fff; padding: 7px 6px; font-size: 9px;
               text-align: left; font-weight: 600; }
    thead th:first-child { border-radius: 0; text-align: center; width: 28px; }
    tbody tr:nth-child(even) { background: #f7f9fc; }
    tbody tr:hover { background: #eef2fb; }
    tbody td { padding: 6px 6px; font-size: 9px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
    tbody td:first-child { text-align: center; color: #888; }
    .badge { display: inline-block; padding: 2px 6px; border-radius: 20px; font-size: 8px; font-weight: 600; }
    .badge-hadir    { background: #d1fae5; color: #065f46; }
    .badge-izin     { background: #fef3c7; color: #92400e; }
    .badge-sakit    { background: #dbeafe; color: #1e40af; }
    .badge-alpha    { background: #fee2e2; color: #991b1b; }

    /* === FOOTER === */
    .footer { margin-top: 16px; display: flex; justify-content: space-between; align-items: flex-end; }
    .footer-left { font-size: 8px; color: #888; }
    .footer-ttd { text-align: center; font-size: 9px; }
    .footer-ttd .ttd-label { margin-bottom: 50px; }
    .footer-ttd .ttd-name { font-weight: 700; border-top: 1px solid #333; padding-top: 3px; }
    .total-row { background: #eef2fb !important; font-weight: 700; }
    .total-row td { color: #1B2A4A; border-top: 2px solid #1B2A4A; }
</style>
</head>
<body>

    {{-- KOP --}}
    <div class="kop">
        @if(\App\Models\Setting::get('school_logo') && file_exists(public_path('storage/' . \App\Models\Setting::get('school_logo'))))
            <img src="{{ public_path('storage/' . \App\Models\Setting::get('school_logo')) }}" alt="Logo" style="width: 60px; height: 60px; border-radius: 8px; flex-shrink: 0; object-fit: cover; border: none; padding: 0; margin-top: 0; margin-right: 0;">
        @else
            <div class="kop-logo">
                {{ collect(explode(' ', \App\Models\Setting::get('school_name', 'SMKN 2 SURABAYA')))->map(fn($w) => substr($w, 0, 1))->take(2)->join('') }}
            </div>
        @endif
        <div class="kop-text">
            <div class="kop-sekolah">{{ \App\Models\Setting::get('school_name', 'SMKN 2 SURABAYA') }}</div>
            <div class="kop-alamat">
                {{ \App\Models\Setting::get('school_address', 'Jl. Contoh No. 1, Surabaya, Jawa Timur') }}
                @if(\App\Models\Setting::get('school_phone'))
                     · Telp. {{ \App\Models\Setting::get('school_phone') }}
                @endif
            </div>
            <div class="kop-alamat">SIMGURU – Sistem Informasi Monitoring Guru</div>
        </div>
    </div>

    {{-- JUDUL --}}
    <div class="laporan-judul">
        <h2>Laporan Rekap Kehadiran Guru</h2>
        <p>Dicetak pada {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y · HH:mm') }} WIB</p>
    </div>

    {{-- INFO FILTER --}}
    <div class="info-filter">
        <div class="info-item">Periode: <span>{{ $periodeLabel }}</span></div>
        @if($guruLabel)
            <div class="info-item">Guru: <span>{{ $guruLabel }}</span></div>
        @endif
        @if($kelasLabel)
            <div class="info-item">Kelas: <span>{{ $kelasLabel }}</span></div>
        @endif
        <div class="info-item">Total Data: <span>{{ $data->count() }} baris</span></div>
    </div>

    {{-- TABEL --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Guru</th>
                <th>NIP</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Kelas</th>
                <th>Mata Pelajaran</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->nama_guru ?? '-' }}</td>
                <td>{{ $row->nik ?? '-' }}</td>
                <td>{{ $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') : '-' }}</td>
                <td>{{ $row->jam_masuk ?? '-' }}</td>
                <td>{{ $row->jam_keluar ?? '-' }}</td>
                <td>{{ $row->kelas ?? '-' }}</td>
                <td>{{ $row->mapel ?? '-' }}</td>
                <td>
                    @php $status = strtolower($row->status ?? 'hadir'); @endphp
                    <span class="badge badge-{{ $status }}">{{ ucfirst($row->status ?? 'Hadir') }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center; color:#888; padding: 20px;">
                    Tidak ada data pada periode ini.
                </td>
            </tr>
            @endforelse
            @if($data->count() > 0)
            <tr class="total-row">
                <td colspan="8" style="text-align:right; padding-right: 10px;">Total Kehadiran:</td>
                <td>{{ $data->count() }} data</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="footer-left">
            Dokumen ini digenerate secara otomatis oleh SIMGURU.<br>
            Halaman ini sah tanpa tanda tangan basah.
        </div>
        <div class="footer-ttd">
            <div class="ttd-label">Mengetahui,<br>Kepala Sekolah</div>
            <div class="ttd-name">{{ \App\Models\Setting::get('headmaster_name') && \App\Models\Setting::get('headmaster_name') !== '-' ? \App\Models\Setting::get('headmaster_name') : '( _________________________ )' }}</div>
            @if(\App\Models\Setting::get('headmaster_nip') && \App\Models\Setting::get('headmaster_nip') !== '-')
                <div style="font-size:8px; margin-top:2px;">NIP. {{ \App\Models\Setting::get('headmaster_nip') }}</div>
            @else
                <div style="font-size:8px; margin-top:2px;">NIP. __________________________</div>
            @endif
        </div>
    </div>

</body>
</html>
