@extends('layouts.admin')
@section('title', (isset($data) ? 'Edit' : 'Tambah') . ' Ketua Kelas')
@section('page_title', (isset($data) ? 'Edit' : 'Tambah') . ' Ketua Kelas')

@section('content')
<div class="max-w-2xl bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-100 p-6">
    <form action="{{ isset($data) ? route('ketuakelas.update', $data->id) : route('ketuakelas.store') }}" method="POST">
        @csrf
        @if(isset($data)) @method('PUT') @endif
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name', $data->name ?? '') }}" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">NISN</label>
            <input type="text" name="nisn" value="{{ old('nisn', $data->nisn ?? '') }}" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Kelas</label>
            <select name="kelas_id" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500">
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelass as $rel)
                <option value="{{ $rel->id }}" @if(old('kelas_id', $data->kelas_id ?? '') == $rel->id) selected @endif>{{ $rel->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition shadow-sm">Simpan</button>
            <a href="{{ route('ketuakelas.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection