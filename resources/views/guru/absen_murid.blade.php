@extends('layouts.guru')
@section('title', 'Absensi Murid - ' . ($absenMasuk->jadwalAjar->mapel->name ?? 'Mapel'))

@section('content')
<div class="p-5 flex flex-col h-full">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ url()->previous() == url()->current() ? route('guru.dashboard') : url()->previous() }}" class="w-10 h-10 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h2 class="text-xl font-heading font-bold text-slate-800">Absen Kelas {{ $absenMasuk->jadwalAjar->kelas->name ?? '-' }}</h2>
            <p class="text-slate-500 text-sm mt-0.5">{{ $absenMasuk->jadwalAjar->mapel->name ?? '-' }}</p>
        </div>
    </div>

    @if($murids->isEmpty())
        <div class="bg-white rounded-2xl p-8 text-center shadow-sm border border-slate-100 mt-4 flex-1 flex flex-col items-center justify-center">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <h3 class="font-bold text-slate-700 text-lg">Belum ada murid</h3>
            <p class="text-slate-500 mt-1">Tidak ada data murid di kelas ini.</p>
        </div>
    @else
        <form action="{{ route('guru.store_absen_murid', $absenMasuk->id) }}" method="POST" class="flex-1 flex flex-col" id="absenForm">
            @csrf
            
            <div class="bg-brand-50 border border-brand-100 rounded-xl p-3 mb-4 flex justify-between items-center">
                <p class="text-brand-700 font-semibold text-sm">Pilih status kehadiran:</p>
                <button type="button" id="selectAllBtn" class="text-brand-600 font-bold text-sm hover:underline">Hadir Semua</button>
            </div>

            <div class="space-y-3 flex-1 overflow-y-auto pb-24">
                @foreach($murids as $murid)
                    @php
                        $status = 'hadir';
                        if ($absenMurids->has($murid->id)) {
                            $status = $absenMurids[$murid->id]->status;
                        }
                    @endphp
                    <div class="bg-white border rounded-xl p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-all duration-200 student-card {{ $status == 'hadir' ? 'border-brand-200 ring-1 ring-brand-100 shadow-sm' : 'border-slate-200' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-sm shrink-0">
                                {{ $murid->no_absen ?? '-' }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 leading-tight">{{ $murid->name }}</h4>
                                <p class="text-xs text-slate-500 mt-0.5">NIS: {{ $murid->nis ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex bg-slate-100 rounded-lg p-1 shrink-0 self-start sm:self-auto">
                            <label class="cursor-pointer">
                                <input type="radio" name="status[{{ $murid->id }}]" value="hadir" class="peer sr-only status-radio" {{ $status == 'hadir' ? 'checked' : '' }}>
                                <div class="px-4 py-1.5 rounded-md text-sm font-semibold text-slate-500 peer-checked:bg-emerald-500 peer-checked:text-white transition-colors">
                                    Hadir
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="status[{{ $murid->id }}]" value="alpa" class="peer sr-only status-radio" {{ $status == 'alpa' ? 'checked' : '' }}>
                                <div class="px-4 py-1.5 rounded-md text-sm font-semibold text-slate-500 peer-checked:bg-rose-500 peer-checked:text-white transition-colors">
                                    Alpa
                                </div>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Sticky Bottom Submit Button -->
            <div class="fixed bottom-0 left-0 right-0 md:left-[260px] bg-white border-t border-slate-200 p-4 shadow-[0_-10px_15px_-3px_rgba(0,0,0,0.05)] z-10 transition-all duration-300" id="bottomBar">
                <div class="max-w-4xl mx-auto flex items-center justify-between gap-4">
                    <div class="hidden sm:block">
                        <p class="text-xs text-slate-500 font-medium">Total Murid: <strong class="text-slate-800">{{ $murids->count() }}</strong></p>
                    </div>
                    <button type="submit" class="w-full sm:w-auto flex-1 sm:flex-none bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 px-8 rounded-xl shadow-md transition active:scale-95 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Absensi
                    </button>
                </div>
            </div>
        </form>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllBtn = document.getElementById('selectAllBtn');
        const radios = document.querySelectorAll('.status-radio[value="hadir"]');
        const cards = document.querySelectorAll('.student-card');
        const allRadios = document.querySelectorAll('.status-radio');
        
        // Handle Sidebar adjustment for bottom bar
        const bottomBar = document.getElementById('bottomBar');
        if(bottomBar) {
            // Jika sidebar-collapsed-wrapper aktif pada main-content-wrapper, sesuaikan left
            const mainWrapper = document.querySelector('.main-content-wrapper');
            
            function adjustBottomBar() {
                if(window.innerWidth >= 768) {
                    if(mainWrapper && mainWrapper.classList.contains('sidebar-collapsed-wrapper')) {
                        bottomBar.style.left = '60px';
                    } else {
                        bottomBar.style.left = '260px'; // Lebar default sidebar
                    }
                } else {
                    bottomBar.style.left = '0';
                }
            }
            
            adjustBottomBar();
            // Listen to Alpine toggles (approximate by observing class changes)
            const observer = new MutationObserver(adjustBottomBar);
            if(mainWrapper) {
                observer.observe(mainWrapper, { attributes: true, attributeFilter: ['class'] });
            }
            window.addEventListener('resize', adjustBottomBar);
        }

        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                radios.forEach(radio => {
                    radio.checked = true;
                    // Trigger change event to update card styling
                    radio.dispatchEvent(new Event('change'));
                });
            });
        }

        // Add styling logic for checked state
        allRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const card = this.closest('.student-card');
                if (this.value === 'hadir' && this.checked) {
                    card.classList.add('border-brand-200', 'ring-1', 'ring-brand-100', 'shadow-sm');
                    card.classList.remove('border-slate-200');
                } else if (this.value === 'alpa' && this.checked) {
                    card.classList.remove('border-brand-200', 'ring-1', 'ring-brand-100', 'shadow-sm');
                    card.classList.add('border-slate-200');
                }
            });
        });
    });
</script>
@endpush
@endsection
