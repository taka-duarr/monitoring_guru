@extends('layouts.admin')
@section('title', (isset($data) ? 'Edit' : 'Tambah') . ' Pengajuan Izin')
@section('page_title', (isset($data) ? 'Edit' : 'Tambah') . ' Pengajuan Izin')

@section('content')
<div class="max-w-2xl bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-100 p-6">
    <form action="{{ isset($data) ? route('izin.update', $data->id) : route('izin.store') }}" method="POST">
        @csrf
        @if(isset($data)) @method('PUT') @endif
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
            <input type="date" name="tanggal_izin" value="{{ old('tanggal_izin', $data->tanggal_izin ?? '') }}" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Judul</label>
            <input type="text" name="judul" value="{{ old('judul', $data->judul ?? '') }}" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Pesan</label>
            <textarea name="pesan" rows="3" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>{{ old('pesan', $data->pesan ?? '') }}</textarea>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Disetujui?</label>
            <select name="approval" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
                <option value="0" @if(old('approval', $data->approval ?? '') == '0') selected @endif>Belum</option>
                <option value="1" @if(old('approval', $data->approval ?? '') == '1') selected @endif>Ya</option>
            </select>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition shadow-sm">Simpan</button>
            <a href="{{ route('izin.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection