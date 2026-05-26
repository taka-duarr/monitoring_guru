@extends('layouts.siswa')
@section('title', 'Dasbor Kelas')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-heading font-bold text-slate-800">Ruang Kelas</h2>
    <p class="text-slate-500 mt-1">
        @if($kelas)
            Kamu adalah Ketua untuk kelas <strong class="text-brand-600">{{ $kelas->name }}</strong>
        @else
            Kamu belum ditugaskan ke kelas manapun.
        @endif
    </p>
</div>

@if($kelas)
    @if(isset($jadwals) && count($jadwals) > 0)
        <div class="space-y-4 mt-6">
            <h3 class="font-bold text-slate-700 mb-3 text-lg">Mata Pelajaran Hari Ini ({{ $hariIni }})</h3>
            
            @foreach($jadwals as $jadwal)
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="inline-block px-2.5 py-1 bg-brand-50 text-brand-700 text-[10px] font-bold uppercase tracking-wider rounded-md mb-2">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</span>
                    <h4 class="font-bold text-lg text-slate-800">{{ $jadwal->mapel->name ?? 'Mapel Tidak Diketahui' }}</h4>
                    <p class="text-sm text-slate-500 mt-1">Guru: {{ $jadwal->guru->name ?? '-' }} • Ruang: {{ $jadwal->ruangan->name ?? '-' }}</p>
                </div>
                <button onclick="showQrModal('{{ $jadwal->id }}', '{{ $jadwal->mapel->name ?? 'Mapel' }}')" class="shrink-0 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-md shadow-brand-500/20 transition active:scale-[0.98]">
                    Generate QR
                </button>
            </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-3xl p-8 text-center shadow-sm border border-slate-100 mt-8">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 mb-4 mx-auto">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="font-bold text-slate-700">Tidak ada jadwal hari ini</h3>
            <p class="text-slate-500 text-sm mt-1">Asyik, kelasmu sedang kosong hari ini!</p>
        </div>
    @endif

    <!-- Modal QR Code -->
    <div id="qr-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeQrModal()"></div>
        
        <!-- Modal Content -->
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-sm relative z-10 overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="qr-modal-content">
            <div class="p-6 text-center">
                <h3 id="qr-mapel-name" class="font-bold text-xl text-slate-800 mb-1">Nama Mapel</h3>
                <p class="text-xs text-slate-500 mb-6">Tunjukkan QR ini ke Guru yang bersangkutan</p>
                
                <div class="flex justify-center mb-6">
                    <div id="qr-container" class="p-4 bg-white border-4 border-slate-100 rounded-2xl shadow-sm inline-block">
                        <div id="qrcode"></div>
                    </div>
                </div>
                
                <button onclick="closeQrModal()" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3 rounded-xl transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Library QR Code Generator -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        let qrcodeObj = null;

        function showQrModal(jadwalId, mapelName) {
            const modal = document.getElementById('qr-modal');
            const modalContent = document.getElementById('qr-modal-content');
            const qrElement = document.getElementById('qrcode');
            
            // Set judul
            document.getElementById('qr-mapel-name').innerText = mapelName;
            
            // Buat payload baru
            const payload = {
                type: 'absen_jadwal',
                jadwal_id: jadwalId,
                timestamp: Date.now()
            };

            // Reset QR jika sudah ada
            qrElement.innerHTML = '';
            
            // Generate QR baru
            new QRCode(qrElement, {
                text: JSON.stringify(payload),
                width: 220,
                height: 220,
                colorDark : "#0f172a",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });

            // Tampilkan Modal dengan animasi
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // Sedikit delay untuk trigger animasi transisi Tailwind
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeQrModal() {
            const modal = document.getElementById('qr-modal');
            const modalContent = document.getElementById('qr-modal-content');
            
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300); // Waktu yang sama dengan durasi transisi
        }
    </script>
@endif

@endsection
