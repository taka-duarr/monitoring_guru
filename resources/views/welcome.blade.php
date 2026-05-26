<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Guru & Absensi</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .bg-pattern {
            background-color: #f0fdf4;
            background-image: radial-gradient(#22c55e 0.5px, transparent 0.5px), radial-gradient(#22c55e 0.5px, #f0fdf4 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="bg-pattern min-h-screen flex flex-col font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white relative overflow-x-hidden">

    <!-- Decorative Blobs -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-brand-500/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-500/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
    <div class="absolute -bottom-8 left-20 w-96 h-96 bg-green-500/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>

    <!-- Main Content -->
    <div class="relative z-10 flex-grow flex items-center justify-center p-6">
        <div class="glass-panel max-w-4xl w-full rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row">
            
            <!-- Left Side: Branding & Info -->
            <div class="md:w-5/12 bg-gradient-to-br from-brand-600 to-brand-900 text-white p-10 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="absolute -bottom-24 -right-24 w-64 h-64 border-4 border-white/10 rounded-full"></div>
                <div class="absolute -top-12 -left-12 w-40 h-40 border-4 border-white/10 rounded-full"></div>
                
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-6 shadow-inner border border-white/30">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h1 class="font-heading text-4xl font-extrabold tracking-tight mb-2">SMK Hebat</h1>
                    <p class="text-brand-100 font-medium text-lg mb-6">Sistem Monitoring Guru & Absensi Cerdas</p>
                    <p class="text-sm text-brand-50 leading-relaxed opacity-90">
                        Platform terintegrasi untuk memudahkan pemantauan jadwal ajar, absensi QR otomatis, dan manajemen kelas secara real-time.
                    </p>
                </div>
                
                <div class="relative z-10 mt-12 md:mt-0">
                    <p class="text-xs text-brand-100/60 font-semibold tracking-wider uppercase">Versi 2.0 &bull; Berbasis Laravel</p>
                </div>
            </div>

            <!-- Right Side: Action Buttons -->
            <div class="md:w-7/12 p-10 flex flex-col justify-center bg-white">
                <h2 class="font-heading text-2xl font-bold text-slate-800 mb-2">Selamat Datang!</h2>
                <p class="text-slate-500 mb-8">Pilih portal masuk sesuai dengan peran Anda di sekolah.</p>

                <div class="space-y-4">
                    <!-- Guru / Admin Portal -->
                    <a href="/login" class="group block p-5 rounded-2xl border-2 border-slate-100 hover:border-brand-500 bg-slate-50 hover:bg-brand-50 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div class="flex-grow">
                                <h3 class="font-bold text-slate-800 text-lg group-hover:text-brand-700 transition-colors">Portal Guru & Admin</h3>
                                <p class="text-sm text-slate-500">Kelola jadwal, perizinan, dan master data.</p>
                            </div>
                            <div class="text-slate-300 group-hover:text-brand-500 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </div>
                    </a>

                    <!-- Ketua Kelas / Scan QR -->
                    <a href="/scan" class="group block p-5 rounded-2xl border-2 border-slate-100 hover:border-blue-500 bg-slate-50 hover:bg-blue-50 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </div>
                            <div class="flex-grow">
                                <h3 class="font-bold text-slate-800 text-lg group-hover:text-blue-700 transition-colors">Portal Siswa & Scan QR</h3>
                                <p class="text-sm text-slate-500">Laporkan status guru dan scan barcode.</p>
                            </div>
                            <div class="text-slate-300 group-hover:text-blue-500 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                    <p class="text-sm text-slate-400">Punya kendala? Hubungi <a href="#" class="text-brand-500 hover:underline font-medium">Administrator</a></p>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
