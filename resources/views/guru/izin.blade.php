@extends('layouts.guru')
@section('title', 'Pengajuan Izin')

@section('content')
<div class="p-5">
    <div class="mb-6">
        <h2 class="text-2xl font-heading font-bold text-slate-800">Pengajuan Izin</h2>
        <p class="text-slate-500 mt-1">Formulir ketidakhadiran harian</p>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
        <form action="#" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal</label>
                <input type="date" name="tanggal" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Izin</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="jenis" value="sakit" class="peer sr-only" required>
                        <div class="text-center px-4 py-3 rounded-xl border border-slate-200 text-slate-600 font-medium peer-checked:bg-brand-50 peer-checked:border-brand-500 peer-checked:text-brand-700 transition">
                            Sakit
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="jenis" value="izin" class="peer sr-only">
                        <div class="text-center px-4 py-3 rounded-xl border border-slate-200 text-slate-600 font-medium peer-checked:bg-brand-50 peer-checked:border-brand-500 peer-checked:text-brand-700 transition">
                            Keperluan Lain
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keterangan / Alasan</label>
                <textarea name="keterangan" rows="3" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition resize-none" placeholder="Tuliskan alasan lengkap..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bukti (Surat Dokter/Lampiran)</label>
                <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 flex flex-col items-center justify-center text-slate-500 hover:bg-slate-50 hover:border-brand-400 transition cursor-pointer">
                    <svg class="w-8 h-8 mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <span class="text-sm font-medium">Upload File (Max: 2MB)</span>
                    <input type="file" name="file" class="hidden">
                </div>
            </div>

            <button type="button" onclick="Swal.fire('Berhasil', 'Pengajuan izin terkirim! Menunggu persetujuan Admin.', 'success')" class="w-full bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-semibold py-3.5 rounded-xl shadow-lg shadow-brand-500/30 transition transform active:scale-[0.98]">
                Kirim Pengajuan
            </button>
        </form>
    </div>
</div>
@endsection
