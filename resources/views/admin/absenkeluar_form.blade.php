@extends('layouts.admin')
@section('title', (isset($data) ? 'Edit' : 'Tambah') . ' Rekap Absen Keluar')
@section('page_title', (isset($data) ? 'Edit' : 'Tambah') . ' Rekap Absen Keluar')

@section('content')
<div class="max-w-2xl bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-100 p-6">
    <form action="{{ isset($data) ? route('absenkeluar.update', $data->id) : route('absenkeluar.store') }}" method="POST">
        @csrf
        @if(isset($data)) @method('PUT') @endif
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">ID Absen Masuk</label>
            <select name="absen_masuk_id" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500">
                <option value="">-- Pilih ID Absen Masuk --</option>
                @foreach($absenMasuks as $rel)
                <option value="{{ $rel->id }}" @if(old('absen_masuk_id', $data->absen_masuk_id ?? '') == $rel->id) selected @endif>{{ $rel->tanggal }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Waktu Keluar</label>
            <input type="time" name="jam_keluar" value="{{ old('jam_keluar', $data->jam_keluar ?? '') }}" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
            <input type="text" name="status" value="{{ old('status', $data->status ?? '') }}" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition shadow-sm">Simpan</button>
            <a href="{{ route('absenkeluar.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection