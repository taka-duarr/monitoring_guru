<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Kelas;
use App\Models\Murid;
use Illuminate\Support\Str;
use App\Imports\MuridImport;
use Maatwebsite\Excel\Facades\Excel;

class MuridController extends Controller
{
    public function index(Kelas $kelas)
    {
        $murids = $kelas->murids()
            ->orderByRaw("FIELD(status, 'aktif') DESC")
            ->orderBy('no_absen', 'asc')
            ->orderBy('name', 'asc')
            ->get();
        return view('admin.murid.index', compact('kelas', 'murids'));
    }

    public function store(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nis' => 'required|unique:murids,nis',
            'name' => 'required|string|max:255',
            'no_absen' => 'nullable|integer',
        ]);

        $kelas->murids()->create([
            'id' => Str::uuid(),
            'nis' => $request->nis,
            'name' => $request->name,
            'no_absen' => $request->no_absen,
            'status' => 'aktif',
        ]);

        return redirect()->route('kelas.murid.index', $kelas->id)->with('success', 'Murid berhasil ditambahkan');
    }

    public function update(Request $request, Kelas $kelas, Murid $murid)
    {
        $request->validate([
            'nis' => 'required|unique:murids,nis,' . $murid->id,
            'name' => 'required|string|max:255',
            'no_absen' => 'nullable|integer',
            'status' => 'required|in:aktif,lulus,pindah,keluar',
        ]);

        $murid->update([
            'nis' => $request->nis,
            'name' => $request->name,
            'no_absen' => $request->no_absen,
            'status' => $request->status,
        ]);

        return redirect()->route('kelas.murid.index', $kelas->id)->with('success', 'Data murid berhasil diperbarui');
    }

    public function destroy(Kelas $kelas, Murid $murid)
    {
        $murid->forceDelete();
        return redirect()->route('kelas.murid.index', $kelas->id)->with('success', 'Murid berhasil dihapus permanen');
    }

    public function downloadTemplate(Kelas $kelas)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-import-murid.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'No Absen', 'NIS', 'Nama Murid'
            ]);

            fputcsv($file, [
                '1', '12345678', 'Budi Santoso'
            ]);

            fputcsv($file, [
                '2', '87654321', 'Siti Aminah'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request, Kelas $kelas)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ], [
            'file.required' => 'Pilih file Excel/CSV terlebih dahulu.',
            'file.mimes' => 'Format file harus berupa Excel (.xlsx, .xls) atau CSV (.csv).',
            'file.max' => 'Ukuran file maksimal 2MB.',
        ]);

        try {
            $import = new MuridImport($kelas->id);
            Excel::import($import, $request->file('file'));

            if (!empty($import->getErrors())) {
                return redirect()->route('kelas.murid.index', $kelas->id)
                    ->with('import_errors', $import->getErrors())
                    ->with('error', 'Gagal Mengimpor Berkas! Terdapat kesalahan pada data berikut:');
            }

            return redirect()->route('kelas.murid.index', $kelas->id)->with('success', 'Data Murid berhasil diimpor!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return redirect()->route('kelas.murid.index', $kelas->id)
                ->with('import_errors', $errorMessages)
                ->with('error', 'Gagal Mengimpor Berkas! Terdapat kesalahan format data.');
        } catch (\Exception $e) {
            return redirect()->route('kelas.murid.index', $kelas->id)->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
