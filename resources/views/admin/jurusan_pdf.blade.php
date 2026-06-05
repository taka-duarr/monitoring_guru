<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Jurusan</title>
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
        <p>Laporan Data Program Keahlian / Jurusan</p>
    </div>

    <div class="meta-info">
        <strong>Tanggal Cetak:</strong> {{ date('d-m-Y H:i') }} <br>
        <strong>Total Record:</strong> {{ $jurusans->count() }} Jurusan
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 10%;">No</th>
                <th style="width: 60%;">Nama Jurusan</th>
                <th style="width: 30%;">Kode Jurusan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jurusans as $index => $j)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $j->name }}</strong></td>
                    <td>{{ $j->kode_jurusan ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh SIMGURU
    </div>
</body>
</html>
