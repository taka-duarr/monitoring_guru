@extends('layouts.admin')
@section('title', (isset($data) ? 'Edit' : 'Tambah') . ' Status Kelas')
@section('page_title', (isset($data) ? 'Edit' : 'Tambah') . ' Status Kelas')

@section('content')
<div class="max-w-2xl bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-100 p-6">
    <form action="{{ isset($data) ? route('statuskelas.update', $data->id) : route('statuskelas.store') }}" method="POST">
        @csrf
        @if(isset($data)) @method('PUT') @endif
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Mapel</label>
            <input type="text" name="mapel" value="{{ old('mapel', $data->mapel ?? '') }}" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Pengajar</label>
            <input type="text" name="pengajar" value="{{ old('pengajar', $data->pengajar ?? '') }}" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Aktif?</label>
            <select name="is_active" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
                <option value="0" @if(old('is_active', $data->is_active ?? '') == '0') selected @endif>Tidak</option>
                <option value="1" @if(old('is_active', $data->is_active ?? '') == '1') selected @endif>Ya</option>
            </select>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition shadow-sm">Simpan</button>
            <a href="{{ route('statuskelas.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection