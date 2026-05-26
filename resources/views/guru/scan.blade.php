@extends('layouts.guru')
@section('title', 'Scan QR Absen')

@section('content')
<div class="p-5 flex flex-col h-full">
    <div class="mb-4 text-center">
        <h2 class="text-xl font-heading font-bold text-slate-800">Scan QR Code Kelas</h2>
        <p class="text-sm text-slate-500 mt-1">Arahkan kamera ke layar HP Ketua Kelas</p>
    </div>

    <div class="flex-1 flex flex-col items-center justify-center">
        <!-- Area Scanner Kamera -->
        <div class="relative w-full max-w-sm aspect-square bg-slate-900 rounded-3xl overflow-hidden shadow-xl border-4 border-slate-800">
            <div id="reader" class="w-full h-full object-cover"></div>
            
            <!-- Panduan Frame Overlay -->
            <div class="absolute inset-0 border-2 border-brand-500/50 m-8 rounded-2xl pointer-events-none"></div>
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="w-48 h-48 border-2 border-brand-400 rounded-xl relative">
                    <div class="absolute -top-2 -left-2 w-6 h-6 border-t-4 border-l-4 border-brand-400"></div>
                    <div class="absolute -top-2 -right-2 w-6 h-6 border-t-4 border-r-4 border-brand-400"></div>
                    <div class="absolute -bottom-2 -left-2 w-6 h-6 border-b-4 border-l-4 border-brand-400"></div>
                    <div class="absolute -bottom-2 -right-2 w-6 h-6 border-b-4 border-r-4 border-brand-400"></div>
                </div>
            </div>
        </div>

        <p class="mt-6 text-sm text-center text-slate-500 max-w-xs">
            Pastikan ruangan cukup terang dan QR Code berada tepat di dalam kotak panduan.
        </p>

        <!-- Hasil Scan Sementara (Untuk simulasi/debug) -->
        <div id="scan-result" class="mt-4 hidden bg-green-50 text-green-700 p-3 rounded-xl border border-green-200 text-sm font-medium w-full max-w-sm text-center">
        </div>
    </div>
</div>

<!-- Library Scanner: html5-qrcode -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        html5QrCode.start({ facingMode: "environment" }, config, 
            (decodedText, decodedResult) => {
                // Berhasil scan, Hentikan kamera
                html5QrCode.stop().then(() => {
                    document.getElementById('scan-result').classList.remove('hidden');
                    document.getElementById('scan-result').innerText = "Memproses QR...";
                    
                    // AJAX POST ke server
                    fetch("{{ route('guru.processQr') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ qr_data: decodedText })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Absen Berhasil!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "{{ route('guru.dashboard') }}";
                            });
                        } else {
                            Swal.fire('Gagal', data.message, 'error').then(() => location.reload());
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Terjadi kesalahan sistem!', 'error').then(() => location.reload());
                    });
                });
            },
            (errorMessage) => {
                // Terus mencari...
            }
        ).catch((err) => {
            console.error("Camera access error:", err);
            Swal.fire('Error', 'Kamera tidak dapat diakses. Pastikan Anda telah memberikan izin.', 'error');
        });
    });
</script>
@endsection
