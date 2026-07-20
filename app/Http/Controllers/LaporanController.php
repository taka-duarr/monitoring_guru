<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\AbsenMasuk;
use App\Models\Izin;
use App\Models\StatusKelas;
use App\Models\JadwalAjar;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\RiwayatLaporan;

use App\Exports\KehadiranExport;
use App\Exports\PerizinanExport;
use App\Exports\KelasKosongExport;
use App\Exports\JadwalAjarExport;

class LaporanController extends Controller
{
    /**
     * Halaman Buat Laporan — tampilkan form konfigurasi.
     */
    public function index()
    {
        $gurus   = Guru::orderBy('name')->get(['id', 'name', 'nik']);
        $kelas   = Kelas::orderBy('name')->get(['id', 'name']);
        $mapels  = Mapel::orderBy('name')->get(['id', 'name']);

        return view('admin.laporan.index', compact('gurus', 'kelas', 'mapels'));
    }

    /**
     * Generate laporan berdasarkan form, simpan ke storage, catat di riwayat.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'jenis_laporan' => 'required|in:rekap_kehadiran,perizinan,kelas_kosong,jadwal_ajar',
            'format'        => 'required|in:pdf,excel',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $jenis         = $request->jenis_laporan;
        $format        = $request->format;
        $tanggalMulai  = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai)->startOfDay() : Carbon::now()->startOfMonth();
        $tanggalAkhir  = $request->tanggal_akhir ? Carbon::parse($request->tanggal_akhir)->endOfDay()   : Carbon::now()->endOfDay();
        $periodeLabel  = $tanggalMulai->format('d/m/Y') . ' – ' . $tanggalAkhir->format('d/m/Y');

        // === Ambil data berdasarkan jenis ===
        [$data, $namaLaporan, $viewData] = match ($jenis) {
            'rekap_kehadiran' => $this->queryKehadiran($request, $tanggalMulai, $tanggalAkhir, $periodeLabel),
            'perizinan'       => $this->queryPerizinan($request, $tanggalMulai, $tanggalAkhir, $periodeLabel),
            'kelas_kosong'    => $this->queryKelasKosong($request, $tanggalMulai, $tanggalAkhir, $periodeLabel),
            'jadwal_ajar'     => $this->queryJadwalAjar($request, $periodeLabel),
        };

        // === Generate file ===
        $timestamp = Carbon::now()->format('Ymd_His');
        $fileBase  = "{$jenis}_{$timestamp}";
        $ext       = $format === 'pdf' ? 'pdf' : 'xlsx';
        $fileName  = "{$fileBase}.{$ext}";
        $storagePath = "laporan/{$fileName}";

        if ($format === 'pdf') {
            $pdfView = match ($jenis) {
                'rekap_kehadiran' => 'admin.laporan.pdf.rekap_kehadiran',
                'perizinan'       => 'admin.laporan.pdf.perizinan',
                'kelas_kosong'    => 'admin.laporan.pdf.kelas_kosong',
                'jadwal_ajar'     => 'admin.laporan.pdf.jadwal_ajar',
            };
            $pdf = Pdf::loadView($pdfView, $viewData)->setPaper('a4', 'landscape');
            Storage::put($storagePath, $pdf->output());
        } else {
            $export = match ($jenis) {
                'rekap_kehadiran' => new KehadiranExport($data, $periodeLabel),
                'perizinan'       => new PerizinanExport($data),
                'kelas_kosong'    => new KelasKosongExport($data),
                'jadwal_ajar'     => new JadwalAjarExport($data),
            };
            Excel::store($export, $storagePath);
        }

        // === Catat ke riwayat ===
        RiwayatLaporan::create([
            'nama_laporan' => $namaLaporan,
            'jenis_laporan' => $jenis,
            'parameter'    => $request->except(['_token', 'format', 'jenis_laporan']),
            'format'       => $format,
            'file_path'    => $storagePath,
            'file_name'    => $fileName,
            'dibuat_oleh'  => Auth::id(),
        ]);

        // === Download langsung ===
        return Storage::download($storagePath, $fileName);
    }

    /**
     * Halaman Riwayat Laporan — daftar semua laporan yang pernah digenerate.
     */
    public function riwayat(Request $request)
    {
        $riwayats = RiwayatLaporan::with('pembuat')
            ->when($request->jenis, fn ($q, $v) => $q->where('jenis_laporan', $v))
            ->when($request->format, fn ($q, $v) => $q->where('format', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.laporan.riwayat', compact('riwayats'));
    }

    /**
     * Re-download file laporan yang sudah tersimpan.
     */
    public function download(RiwayatLaporan $riwayat)
    {
        if (!Storage::exists($riwayat->file_path)) {
            return back()->with('error', 'File tidak ditemukan. Mungkin sudah dihapus dari server.');
        }

        return Storage::download($riwayat->file_path, $riwayat->file_name);
    }

    /**
     * Hapus riwayat laporan + file dari storage.
     */
    public function destroy(RiwayatLaporan $riwayat)
    {
        if (Storage::exists($riwayat->file_path)) {
            Storage::delete($riwayat->file_path);
        }
        $riwayat->delete();

        return redirect()->route('laporan.riwayat')->with('success', 'Riwayat laporan berhasil dihapus.');
    }

    // =========================================================================
    // PRIVATE QUERY HELPERS
    // =========================================================================

    private function queryKehadiran(Request $req, Carbon $mulai, Carbon $akhir, string $periodeLabel): array
    {
        $query = AbsenMasuk::with(['guru', 'kelas', 'jadwalAjar.mapel', 'absenKeluar'])
            ->whereBetween('tanggal', [$mulai->toDateString(), $akhir->toDateString()]);

        if ($req->guru_id) {
            $query->where('guru_id', $req->guru_id);
        }
        if ($req->kelas_id) {
            $query->where('kelas_id', $req->kelas_id);
        }

        $raw = $query->get();

        $data = collect();
        foreach($raw as $a) {
            $data->push((object)[
                'nama_guru'   => $a->guru?->name ?? '-',
                'nik'         => $a->guru?->nik  ?? '-',
                'tanggal'     => $a->tanggal,
                'jam_masuk'   => substr($a->jam_masuk, 0, 5),
                'jam_keluar'  => $a->absenKeluar ? substr($a->absenKeluar->jam_keluar, 0, 5) : '-',
                'kelas'       => $a->kelas?->name ?? '-',
                'mapel'       => $a->jadwalAjar?->mapel?->name ?? '-',
                'status'      => 'Hadir',
                'keterangan'  => '-',
            ]);
        }

        // Ambil data Izin
        $izinQuery = Izin::with(['guru', 'jadwalAjar.kelas', 'jadwalAjar.mapel'])
            ->where('approval', true)
            ->whereBetween('tanggal_izin', [$mulai->toDateString(), $akhir->toDateString()]);
            
        if ($req->guru_id) {
            $izinQuery->where('guru_id', $req->guru_id);
        }
        
        $izins = $izinQuery->get();
        foreach($izins as $i) {
            // Filter by kelas if requested (since izin might be tied to a jadwal_ajar or all day)
            if ($req->kelas_id && $i->jadwal_ajar_id && $i->jadwalAjar && $i->jadwalAjar->kelas_id != $req->kelas_id) {
                continue;
            }
            if ($req->kelas_id && !$i->jadwal_ajar_id) {
                // If it's an all-day leave, we should include it but maybe the class is generic.
                // We'll just list it.
            }

            $data->push((object)[
                'nama_guru'   => $i->guru?->name ?? '-',
                'nik'         => $i->guru?->nik  ?? '-',
                'tanggal'     => $i->tanggal_izin,
                'jam_masuk'   => '-',
                'jam_keluar'  => '-',
                'kelas'       => $i->jadwal_ajar_id ? ($i->jadwalAjar?->kelas?->name ?? '-') : 'Semua Kelas',
                'mapel'       => $i->jadwal_ajar_id ? ($i->jadwalAjar?->mapel?->name ?? '-') : '-',
                'status'      => 'Izin',
                'keterangan'  => $i->judul . ' - ' . $i->pesan,
            ]);
        }
        
        // Urutkan berdasarkan tanggal dan nama guru
        $data = $data->sortBy([
            ['tanggal', 'desc'],
            ['nama_guru', 'asc']
        ])->values();

        $guruLabel  = $req->guru_id  ? (\App\Models\User::find($req->guru_id)?->name ?? '') : '';
        $kelasLabel = $req->kelas_id ? (\App\Models\Kelas::find($req->kelas_id)?->name ?? '') : '';

        $namaLaporan = 'Rekap Kehadiran' . ($guruLabel ? " — {$guruLabel}" : '') . " ({$periodeLabel})";

        $viewData = compact('data', 'periodeLabel', 'guruLabel', 'kelasLabel');

        return [$data, $namaLaporan, $viewData];
    }

    private function queryPerizinan(Request $req, Carbon $mulai, Carbon $akhir, string $periodeLabel): array
    {
        $query = Izin::with(['guru', 'jadwalAjar.kelas'])
            ->whereBetween('tanggal_izin', [$mulai->toDateString(), $akhir->toDateString()]);

        if ($req->jenis_izin) {
            $jenis = $req->jenis_izin;
            if ($jenis === 'sakit') {
                $query->where('judul', 'like', '%sakit%');
            } elseif ($jenis === 'dinas') {
                $query->where('judul', 'like', '%dinas%');
            } elseif ($jenis === 'izin') {
                $query->where(function ($q) {
                    $q->where('judul', 'like', '%izin%')
                      ->orWhere('judul', 'like', '%keluarga%')
                      ->orWhere('judul', 'like', '%keperluan%');
                });
            } else {
                $query->where('judul', 'not like', '%sakit%')
                      ->where('judul', 'not like', '%dinas%')
                      ->where('judul', 'not like', '%izin%')
                      ->where('judul', 'not like', '%keluarga%')
                      ->where('judul', 'not like', '%keperluan%');
            }
        }

        $raw = $query->get();

        $data = $raw->map(function ($izin) {
            $judulLower = strtolower($izin->judul);
            if (str_contains($judulLower, 'sakit')) {
                $jenisIzin = 'sakit';
            } elseif (str_contains($judulLower, 'dinas')) {
                $jenisIzin = 'dinas';
            } elseif (str_contains($judulLower, 'izin') || str_contains($judulLower, 'keluarga') || str_contains($judulLower, 'keperluan')) {
                $jenisIzin = 'izin';
            } else {
                $jenisIzin = 'lainnya';
            }

            $statusStr = $izin->approval ? 'Disetujui' : 'Menunggu';

            return (object)[
                'nama_guru'  => $izin->guru?->name ?? $izin->jadwalAjar?->guru?->name ?? '-',
                'nik'        => $izin->guru?->nik ?? $izin->jadwalAjar?->guru?->nik ?? '-',
                'tanggal'    => $izin->tanggal_izin,
                'jenis_izin' => $jenisIzin,
                'keterangan' => $izin->judul . ($izin->pesan ? ' - ' . $izin->pesan : ''),
                'status'     => $statusStr,
            ];
        });

        $jenisIzinLabel = $req->jenis_izin ? ucfirst($req->jenis_izin) : 'Semua Jenis';
        $namaLaporan    = "Perizinan Guru ({$periodeLabel})";
        $viewData       = compact('data', 'periodeLabel', 'jenisIzinLabel');

        return [$data, $namaLaporan, $viewData];
    }

    private function queryKelasKosong(Request $req, Carbon $mulai, Carbon $akhir, string $periodeLabel): array
    {
        $query = StatusKelas::with('kelas')
            ->whereBetween('created_at', [$mulai, $akhir]);

        if ($req->kelas_id) {
            $query->where('kelas_id', $req->kelas_id);
        }

        $raw = $query->get();

        $data = $raw->map(fn ($sk) => (object)[
            'tanggal'    => $sk->created_at?->toDateString(),
            'kelas'      => $sk->kelas?->name ?? '-',
            'jam'        => $sk->jam ?? '-',
            'mapel'      => $sk->mapel ?? '-',
            'guru'       => $sk->guru ?? '-',
            'status'     => $sk->status ?? '-',
            'keterangan' => $sk->keterangan ?? '-',
        ]);

        $kelasLabel  = $req->kelas_id ? (Kelas::find($req->kelas_id)?->name ?? '') : '';
        $namaLaporan = "Kelas Kosong ({$periodeLabel})";
        $viewData    = compact('data', 'periodeLabel', 'kelasLabel');

        return [$data, $namaLaporan, $viewData];
    }

    private function queryJadwalAjar(Request $req, string $periodeLabel): array
    {
        $query = JadwalAjar::with(['guru', 'mapel', 'kelas', 'ruangan']);

        if ($req->guru_id) {
            $query->where('guru_id', $req->guru_id);
        }
        if ($req->kelas_id) {
            $query->where('kelas_id', $req->kelas_id);
        }
        if ($req->mapel_id) {
            $query->where('mapel_id', $req->mapel_id);
        }

        $raw = $query->get();

        $data = $raw->map(fn ($j) => (object)[
            'guru'    => $j->guru?->name  ?? '-',
            'nik'     => $j->guru?->nik   ?? '-',
            'mapel'   => $j->mapel?->name ?? '-',
            'kelas'   => $j->kelas?->name ?? '-',
            'hari'    => $j->hari    ?? '-',
            'jam'     => ($j->jam_mulai && $j->jam_selesai) ? "{$j->jam_mulai} - {$j->jam_selesai}" : '-',
            'ruangan' => $j->ruangan?->name ?? '-',
        ]);

        $guruLabel   = $req->guru_id  ? (Guru::find($req->guru_id)?->name  ?? '') : '';
        $kelasLabel  = $req->kelas_id ? (Kelas::find($req->kelas_id)?->name ?? '') : '';
        $mapelLabel  = $req->mapel_id ? (Mapel::find($req->mapel_id)?->name ?? '') : '';

        $namaLaporan = 'Jadwal Mengajar' . ($guruLabel ? " – {$guruLabel}" : '');
        $viewData    = compact('data', 'periodeLabel', 'guruLabel', 'kelasLabel', 'mapelLabel');

        return [$data, $namaLaporan, $viewData];
    }
}
