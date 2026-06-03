@extends('layouts.admin')

@section('title', 'Dashboard - SIMGURU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-wrapper">
    <!-- Header Page Title (Scannable Summary) -->
    <div class="mb-4">
        <h2 class="text-2xl font-bold tracking-tight text-primary-900">Ringkasan Pemantauan Hari Ini</h2>
        <p class="text-sm text-neutral-500">Informasi kehadiran guru dan status kelas tanggal {{ date('d F Y') }}</p>
    </div>

    <!-- ROW 1: STAT CARDS -->
    <div class="dashboard-stats-grid">
        <!-- Card 1: Total Guru -->
        <div class="stat-card stat-card-primary">
            <div class="stat-card-icon-wrapper">
                <i class="ti ti-users"></i>
            </div>
            <div class="stat-card-info">
                <span class="stat-card-label">Total Guru</span>
                <span class="stat-card-value">{{ $stats['total_guru'] }}</span>
                <span class="stat-card-trend">
                    <i class="ti ti-arrow-up-right"></i> {{ $stats['active_guru_change'] }} aktif
                </span>
            </div>
        </div>

        <!-- Card 2: Guru Hadir -->
        <div class="stat-card stat-card-success">
            <div class="stat-card-icon-wrapper">
                <i class="ti ti-user-check"></i>
            </div>
            <div class="stat-card-info">
                <span class="stat-card-label">Hadir</span>
                <span class="stat-card-value">{{ $stats['hadir'] }}</span>
                <span class="stat-card-trend">
                    <i class="ti ti-plus"></i>{{ $stats['change_hadir'] }} vs kemarin
                </span>
            </div>
        </div>

        <!-- Card 3: Tidak Hadir -->
        <div class="stat-card stat-card-danger">
            <div class="stat-card-icon-wrapper">
                <i class="ti ti-user-x"></i>
            </div>
            <div class="stat-card-info">
                <span class="stat-card-label">Tidak Hadir</span>
                <span class="stat-card-value">{{ $stats['tidak_hadir'] }}</span>
                <span class="stat-card-trend">
                    {{ $stats['change_tidak_hadir'] }} vs kemarin
                </span>
            </div>
        </div>

        <!-- Card 4: Persentase Kehadiran -->
        <div class="stat-card stat-card-warning">
            <div class="stat-card-icon-wrapper">
                <i class="ti ti-chart-pie"></i>
            </div>
            <div class="stat-card-info">
                <span class="stat-card-label">% Kehadiran</span>
                <span class="stat-card-value">{{ number_format($stats['persen_hadir'], 1, ',', '.') }}%</span>
                <span class="stat-card-trend">
                    <i class="ti ti-trending-up"></i> ▲ 2,1% vs kemarin
                </span>
            </div>
        </div>
    </div>

    <!-- ROW 2: CHARTS -->
    <div class="dashboard-grid-2col">
        <!-- Bar Chart Card (60%) -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tren Kehadiran 7 Hari Terakhir</h3>
                <span class="text-xs text-muted">Statistik harian</span>
            </div>
            <div class="chart-container">
                <canvas id="attendanceBarChart"></canvas>
            </div>
        </div>

        <!-- Donut Chart Card (40%) -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Distribusi Status Hari Ini</h3>
                <span class="text-xs text-muted">Persentase status</span>
            </div>
            <div class="chart-container">
                <canvas id="distributionDonutChart"></canvas>
            </div>
        </div>
    </div>

    <!-- ROW 3: TABEL KETIDAKHADIRAN & JADWAL KELAS KOSONG -->
    <div class="dashboard-grid-2col">
        <!-- Table Card (60%) -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Guru Tidak Hadir Hari Ini</h3>
                <span class="badge badge-neutral">{{ $tidak_hadir_hari_ini->count() }} orang</span>
            </div>

            @if($tidak_hadir_hari_ini->isEmpty())
                <div class="empty-state-wrapper">
                    <div class="empty-state-icon">
                        <i class="ti ti-circle-check"></i>
                    </div>
                    <span class="empty-state-title">Semua guru hadir hari ini!</span>
                    <span class="empty-state-sub">Tidak ada ketidakhadiran yang dilaporkan.</span>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama Guru</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tidak_hadir_hari_ini as $guru)
                                <tr>
                                    <td class="d-flex align-center gap-2">
                                        @php
                                            $initials = collect(explode(' ', $guru->name))->map(fn($n) => substr($n, 0, 1))->take(2)->join('');
                                            $colorIndex = (ord(substr($guru->name, 0, 1)) % 9) + 1;
                                        @endphp
                                        <div class="avatar avatar-sm avatar-bg-{{ $colorIndex }}">
                                            {{ $initials }}
                                        </div>
                                        <span class="font-semibold">{{ $guru->name }}</span>
                                    </td>
                                    <td>{{ $guru->mapel }}</td>
                                    <td>{{ $guru->kelas }}</td>
                                    <td>
                                        @if($guru->status == 'Sakit')
                                            <span class="badge badge-warning">Sakit</span>
                                        @elseif($guru->status == 'Izin')
                                            <span class="badge badge-info">Izin</span>
                                        @else
                                            <span class="badge badge-danger">Alpha</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $guru->keterangan }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('izin.index') }}" class="card-footer-link">Lihat semua pengajuan izin →</a>
            @endif
        </div>

        <!-- Schedule list Card (40%) -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Jadwal Kelas Kosong Hari Ini</h3>
                <span class="badge badge-danger">{{ $jadwal_kosong->count() }} kelas</span>
            </div>

            @if($jadwal_kosong->isEmpty())
                <div class="empty-state-wrapper">
                    <div class="empty-state-icon">
                        <i class="ti ti-circle-check"></i>
                    </div>
                    <span class="empty-state-title">Semua kelas terisi!</span>
                    <span class="empty-state-sub">Seluruh jadwal ajar hari ini memiliki guru pengganti.</span>
                </div>
            @else
                <ul class="schedule-list">
                    @foreach($jadwal_kosong as $jadwal)
                        <li class="schedule-item schedule-empty">
                            <div class="schedule-item-info">
                                <span class="schedule-item-time">Jam: {{ $jadwal->jam }}</span>
                                <span class="schedule-item-title">{{ $jadwal->kelas }} – {{ $jadwal->mapel }}</span>
                                <span class="schedule-item-meta">Guru tidak hadir tanpa pengganti</span>
                            </div>
                            <span class="badge badge-danger schedule-item-badge">KOSONG</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Chart.js (via CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Retrieve and parse Laravel dashboard chart datasets
            const barData = @json($chartData);
            const donutData = @json($donutData);
            
            // Format percentage with Indonesian style
            const persenHadirText = "{{ number_format($stats['persen_hadir'], 1, ',', '.') }}%";
            
            // Initialize charts via globally registered js script
            initializeDashboardCharts(barData, donutData, persenHadirText);
        });
    </script>
@endpush
