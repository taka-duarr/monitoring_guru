@extends('layouts.admin')
@section('title', 'Dashboard - Monitoring Guru')
@section('page_title', 'Beranda Dashboard')

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center">
        <div class="w-14 h-14 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mr-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium">Total Guru</p>
            <h3 class="text-2xl font-bold text-slate-800">{{ \App\Models\Guru::count() }}</h3>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center">
        <div class="w-14 h-14 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mr-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium">Total Kelas</p>
            <h3 class="text-2xl font-bold text-slate-800">{{ \App\Models\Kelas::count() }}</h3>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center">
        <div class="w-14 h-14 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mr-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium">Total Jadwal Ajar</p>
            <h3 class="text-2xl font-bold text-slate-800">{{ \App\Models\JadwalAjar::count() }}</h3>
        </div>
    </div>
</div>

<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-brand-600 to-brand-800 rounded-3xl p-8 text-white shadow-lg relative overflow-hidden mb-8">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full mix-blend-overlay filter blur-xl transform translate-x-1/2 -translate-y-1/2"></div>
    <div class="relative z-10">
        <h2 class="text-3xl font-heading font-bold mb-2">Selamat datang kembali, {{ Auth::user()->name }}! 👋</h2>
        <p class="text-brand-100 text-lg max-w-2xl">
            Aplikasi Monitoring Guru kini berjalan dengan Custom UI yang sangat ringan dan mulus.
        </p>
    </div>
</div>
@endsection
