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
            ->whereBetween('created_at', [$mulai, $akhir]);

        if ($req->guru_id) {
            $query->where('guru_id', $req->guru_id);
        }
        if ($req->kelas_id) {
            $query->where('kelas_id', $req->kelas_id);
        }

        $raw = $query->get();

        $data = $raw->map(fn ($a) => (object)[
            'nama_guru'   => $a->guru?->name ?? '-',
            'nik'         => $a->guru?->nik  ?? '-',
            'tanggal'     => $a->created_at?->toDateString(),
            'jam_masuk'   => $a->created_at?->format('H:i'),
            'jam_keluar'  => $a->absenKeluar?->created_at?->format('H:i') ?? '-',
            'kelas'       => $a->kelas?->name ?? '-',
            'mapel'       => $a->jadwalAjar?->mapel?->name ?? '-',
            'status'      => 'Hadir',
            'keterangan'  => '-',
        ]);

        $guruLabel  = $req->guru_id  ? (Guru::find($req->guru_id)?->name ?? '') : '';
        $kelasLabel = $req->kelas_id ? (Kelas::find($req->kelas_id)?->name ?? '') : '';

        $namaLaporan = 'Rekap Kehadiran' . ($guruLabel ? " – {$guruLabel}" : '') . " ({$periodeLabel})";

        $viewData = compact('data', 'periodeLabel', 'guruLabel', 'kelasLabel');

        return [$data, $namaLaporan, $viewData];
    }

    private function queryPerizinan(Request $req, Carbon $mulai, Carbon $akhir, string $periodeLabel): array
    {
        $query = Izin::with(['jadwalAjar.guru', 'jadwalAjar.kelas'])
            ->whereBetween('created_at', [$mulai, $akhir]);

        if ($req->jenis_izin) {
            $query->where('jenis_izin', $req->jenis_izin);
        }

        $raw = $query->get();

        $data = $raw->map(fn ($izin) => (object)[
            'nama_guru'  => $izin->jadwalAjar?->guru?->name ?? '-',
            'nik'        => $izin->jadwalAjar?->guru?->nik  ?? '-',
            'tanggal'    => $izin->created_at?->toDateString(),
            'jenis_izin' => $izin->jenis_izin ?? '-',
            'keterangan' => $izin->keterangan ?? '-',
            'status'     => $izin->status ?? 'Diajukan',
        ]);

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
            'jam'     => $j->jam     ?? '-',
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
