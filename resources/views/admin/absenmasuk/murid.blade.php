@extends('layouts.admin')

@section('title', 'Absensi Murid - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
@endpush

@section('content')
<!-- Header Page Title -->
<div class="d-flex align-center justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-900">Kehadiran Murid Sesi</h2>
        <p class="text-sm text-neutral-500">
            Tanggal: <strong>{{ \Carbon\Carbon::parse($absenmasuk->tanggal)->translatedFormat('d M Y') }}</strong> | 
            Guru: <strong>{{ $absenmasuk->guru->name ?? '-' }}</strong> | 
            Kelas: <strong>{{ $absenmasuk->kelas->name ?? '-' }}</strong> | 
            Mapel: <strong>{{ $absenmasuk->jadwalAjar->mapel->name ?? '-' }}</strong>
        </p>
    </div>
    <!-- Back Button -->
    <a href="{{ route('absenmasuk.index') }}" class="btn btn-secondary d-flex align-center gap-2" style="text-decoration: none;">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<!-- MAIN DATA TABLE SECTION -->
<div class="table-wrapper card p-0 overflow-hidden">
    @if($murids->isEmpty())
        <div class="table-empty-state">
            <div class="table-empty-icon">
                <i class="ti ti-users"></i>
            </div>
            <span class="table-empty-title">Tidak ada data murid</span>
            <span class="table-empty-sub">Belum ada murid terdaftar untuk kelas ini.</span>
        </div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th class="col-no">No Absen</th>
                    <th>NIS</th>
                    <th>Nama Lengkap</th>
                    <th class="col-center">Status Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($murids as $row)
                    @php
                        $status = 'hadir';
                        if ($absenMurids->has($row->id)) {
                            $status = $absenMurids[$row->id]->status;
                        }
                    @endphp
                    <tr>
                        <td class="col-no font-semibold">
                            {{ $row->no_absen ?? '-' }}
                        </td>
                        <td>
                            {{ $row->nis }}
                        </td>
                        <td>
                            <span class="font-medium text-neutral-800">{{ $row->name }}</span>
                        </td>
                        <td class="col-center">
                            @if(strtolower($status) == 'hadir')
                                <span class="badge badge-success">Hadir</span>
                            @else
                                <span class="badge badge-danger">Alpa</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
