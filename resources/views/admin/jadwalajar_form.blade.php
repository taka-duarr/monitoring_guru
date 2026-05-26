@extends('layouts.admin')
@section('title', (isset($data) ? 'Edit' : 'Tambah') . ' Jadwal Ajar')
@section('page_title', (isset($data) ? 'Edit' : 'Tambah') . ' Jadwal Ajar')

@section('content')
<div class="max-w-2xl bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-100 p-6">
    <form action="{{ isset($data) ? route('jadwalajar.update', $data->id) : route('jadwalajar.store') }}" method="POST">
        @csrf
        @if(isset($data)) @method('PUT') @endif
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Guru Pengajar</label>
            <select name="guru_id" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
                <option value="">-- Pilih Guru --</option>
                @foreach($gurus as $g)
                <option value="{{ $g->id }}" @if(old('guru_id', $data->guru_id ?? '') == $g->id) selected @endif>{{ $g->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Mata Pelajaran</label>
            <select name="mapel_id" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
                <option value="">-- Pilih Mata Pelajaran --</option>
                @foreach($mapels as $m)
                <option value="{{ $m->id }}" @if(old('mapel_id', $data->mapel_id ?? '') == $m->id) selected @endif>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Kelas</label>
            <select name="kelas_id" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelas as $k)
                <option value="{{ $k->id }}" @if(old('kelas_id', $data->kelas_id ?? '') == $k->id) selected @endif>{{ $k->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Ruangan</label>
            <select name="ruangan_id" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
                <option value="">-- Pilih Ruangan --</option>
                @foreach($ruangans as $r)
                <option value="{{ $r->id }}" @if(old('ruangan_id', $data->ruangan_id ?? '') == $r->id) selected @endif>{{ $r->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Hari</label>
            <select name="hari" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
                <option value="Senin" @if(old('hari', $data->hari ?? '') == 'Senin') selected @endif>Senin</option>
                <option value="Selasa" @if(old('hari', $data->hari ?? '') == 'Selasa') selected @endif>Selasa</option>
                <option value="Rabu" @if(old('hari', $data->hari ?? '') == 'Rabu') selected @endif>Rabu</option>
                <option value="Kamis" @if(old('hari', $data->hari ?? '') == 'Kamis') selected @endif>Kamis</option>
                <option value="Jumat" @if(old('hari', $data->hari ?? '') == 'Jumat') selected @endif>Jumat</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Jam Mulai</label>
            <input type="time" name="jam_mulai" value="{{ old('jam_mulai', $data->jam_mulai ?? '') }}" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Jam Selesai</label>
            <input type="time" name="jam_selesai" value="{{ old('jam_selesai', $data->jam_selesai ?? '') }}" class="w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500" required>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition shadow-sm">Simpan</button>
            <a href="{{ route('jadwalajar.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection