<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Guru</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1B2F4E;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            color: #1B2F4E;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 12px;
            color: #666666;
        }
        .meta-info {
            margin-bottom: 15px;
            font-size: 10px;
            color: #555555;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #1B2F4E;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid #1B2F4E;
            font-size: 10px;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 8px 10px;
            border: 1px solid #E5E7EB;
        }
        .data-table tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background-color: #DCFCE7; color: #16A34A; }
        .badge-warning { background-color: #FEF3C7; color: #D97706; }
        .badge-neutral { background-color: #F3F4F6; color: #4B5563; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
            color: #777777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sistem Informasi Monitoring Guru</h1>
        <p>Laporan Data Guru dan Staff Pengampu Kelas</p>
    </div>

    <div class="meta-info">
        <strong>Tanggal Cetak:</strong> {{ date('d-m-Y H:i') }} <br>
        <strong>Total Record:</strong> {{ $gurus->count() }} Guru
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Lengkap</th>
                <th style="width: 15%;">NIK / NIP</th>
                <th style="width: 20%;">Mata Pelajaran</th>
                <th style="width: 20%;">Kelas Pengampu</th>
                <th style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gurus as $index => $guru)
                @php
                    $mapels = $guru->jadwalAjars->pluck('mapel.name')->unique()->join(', ');
                    $kelas = $guru->jadwalAjars->pluck('kelas.name')->unique()->join(', ');
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $guru->name }}</strong></td>
                    <td style="font-family: monospace;">{{ $guru->nik }}</td>
                    <td>{{ $mapels ?: '-' }}</td>
                    <td>{{ $kelas ?: '-' }}</td>
                    <td>
                        @if(strtolower($guru->status) == 'aktif')
                            <span class="badge badge-success">Aktif</span>
                        @elseif(strtolower($guru->status) == 'cuti')
                            <span class="badge badge-warning">Cuti</span>
                        @else
                            <span class="badge badge-neutral">Pensiun</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh SIMGURU SMAN X Surabaya
    </div>
</body>
</html>
