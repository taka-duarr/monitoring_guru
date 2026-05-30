@extends('layouts.admin')
@section('title', (isset($data) ? 'Edit' : 'Tambah') . ' Kelas')
@section('page_title', (isset($data) ? 'Edit' : 'Tambah') . ' Kelas')

@section('content')
<div class="max-w-2xl bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-100 p-6">
    <form action="{{ isset($data) ? route('kelas.update', $data->id) : route('kelas.store') }}" method="POST">
        @csrf
        @if(isset($data)) @method('PUT') @endif
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Kelas</label>
            <input type="text" name="name" value="{{ old('name', $data->name ?? '') }}" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Angkatan</label>
            <select name="angkatan_id" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500">
                <option value="">-- Pilih Angkatan --</option>
                @foreach($angkatans as $ang)
                <option value="{{ $ang->id }}" @if(old('angkatan_id', $data->angkatan_id ?? '') == $ang->id) selected @endif>{{ $ang->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Grade</label>
            <select name="grade" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
                <option value="">-- Pilih Grade --</option>
                <option value="10" @if(old('grade', $data->grade ?? '') == '10') selected @endif>10</option>
                <option value="11" @if(old('grade', $data->grade ?? '') == '11') selected @endif>11</option>
                <option value="12" @if(old('grade', $data->grade ?? '') == '12') selected @endif>12</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Jurusan</label>
            <select name="jurusan_id" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500">
                <option value="">-- Pilih Jurusan --</option>
                @foreach($jurusans as $rel)
                <option value="{{ $rel->id }}" @if(old('jurusan_id', $data->jurusan_id ?? '') == $rel->id) selected @endif>{{ $rel->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition shadow-sm">Simpan</button>
            <a href="{{ route('kelas.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection