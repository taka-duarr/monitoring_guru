@extends('layouts.admin')
@section('title', (isset($data) ? 'Edit' : 'Tambah') . ' Guru')
@section('page_title', (isset($data) ? 'Edit' : 'Tambah') . ' Guru')

@section('content')
<div class="max-w-2xl bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-100 p-6">
    <form action="{{ isset($data) ? route('guru.update', $data->id) : route('guru.store') }}" method="POST">
        @csrf
        @if(isset($data)) @method('PUT') @endif
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name', $data->name ?? '') }}" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">NIK</label>
            <input type="text" name="nik" value="{{ old('nik', $data->nik ?? '') }}" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Jabatan</label>
            <select name="jabatan" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
                <option value="guru" @if(old('jabatan', $data->jabatan ?? '') == 'guru') selected @endif>Guru</option>
                <option value="admin" @if(old('jabatan', $data->jabatan ?? '') == 'admin') selected @endif>Admin</option>
            </select>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition shadow-sm">Simpan</button>
            <a href="{{ route('guru.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection