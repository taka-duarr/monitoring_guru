<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem Informasi Monitoring Guru</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            900: '#0F1E32',
                            800: '#1B2F4E',
                            700: '#1E3A5F',
                            600: '#24497A',
                        },
                        brand: {
                            500: '#2563EB',
                            600: '#1D4ED8',
                            400: '#60A5FA',
                            100: '#EFF6FF',
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-delayed': 'float 6s ease-in-out 2s infinite',
                        'float-slow': 'float 8s ease-in-out 1s infinite',
                        'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Left panel gradient */
        .left-panel {
            background: linear-gradient(145deg, #0F1E32 0%, #1B2F4E 40%, #1E3A5F 70%, #2453A0 100%);
        }

        /* Floating grid dots */
        .dot-grid {
            background-image: radial-gradient(rgba(96, 165, 250, 0.25) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        /* Glass card */
        .glass-card {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        /* Animated gradient ring */
        .gradient-ring {
            background: conic-gradient(from 0deg, #2563EB, #60A5FA, #1D4ED8, #2563EB);
            animation: spin 4s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Input focus animation */
        .form-input {
            transition: all 0.2s ease;
        }
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        /* Submit button shine effect */
        .btn-submit {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .btn-submit::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -75%;
            width: 50%;
            height: 200%;
            background: rgba(255,255,255,0.15);
            transform: skewX(-25deg);
            transition: left 0.5s ease;
        }
        .btn-submit:hover::after {
            left: 125%;
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        }
        .btn-submit:active {
            transform: translateY(0);
        }

        /* Fade-in animation for form elements */
        .anim-1 { animation: fadeInUp 0.5s ease-out 0.1s both; }
        .anim-2 { animation: fadeInUp 0.5s ease-out 0.2s both; }
        .anim-3 { animation: fadeInUp 0.5s ease-out 0.3s both; }
        .anim-4 { animation: fadeInUp 0.5s ease-out 0.4s both; }
        .anim-5 { animation: fadeInUp 0.5s ease-out 0.5s both; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Floating icon cards on left panel */
        .stat-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
        }
    </style>
</head>
<body class="min-h-screen flex antialiased bg-gray-50">

    <!-- ===================== LEFT PANEL ===================== -->
    <div class="hidden lg:flex left-panel w-1/2 xl:w-5/12 relative flex-col justify-between p-12 overflow-hidden">

        <!-- Dot grid overlay -->
        <div class="dot-grid absolute inset-0 opacity-60"></div>

        <!-- Glowing orbs -->
        <div class="absolute top-20 right-10 w-80 h-80 rounded-full opacity-20" style="background: radial-gradient(circle, #2563EB 0%, transparent 70%);"></div>
        <div class="absolute bottom-20 left-5 w-64 h-64 rounded-full opacity-15" style="background: radial-gradient(circle, #60A5FA 0%, transparent 70%);"></div>

        <!-- Top logo area -->
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg p-1">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <span class="text-white font-bold text-lg tracking-tight">Monitoring Guru</span>
            </div>
        </div>

        <!-- Center content -->
        <div class="relative z-10 space-y-8">
            <div>
                <p class="text-brand-400 font-semibold text-sm tracking-widest uppercase mb-3">Sistem Informasi</p>
                <h2 class="text-white font-bold text-4xl xl:text-5xl leading-tight">
                    Sistem Absensi<br>
                    <span style="background: linear-gradient(90deg, #60A5FA, #93C5FD); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">& Jurnal Mengajar</span>
                </h2>
                <p class="text-slate-400 mt-4 text-base leading-relaxed max-w-sm">
                    Platform terpadu untuk pencatatan absensi, pemantauan jadwal kelas, dan laporan secara real-time.
                </p>
            </div>

            <!-- Feature stat cards -->
            <div class="space-y-3">
                <div class="stat-card rounded-2xl px-5 py-4 flex items-center gap-4 animate-float">
                    <div class="w-10 h-10 rounded-xl bg-brand-500/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm">Absensi Real-time</p>
                        <p class="text-slate-400 text-xs mt-0.5">Rekap kehadiran otomatis setiap hari</p>
                    </div>
                </div>

                <div class="stat-card rounded-2xl px-5 py-4 flex items-center gap-4 animate-float-delayed">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm">Manajemen Jadwal</p>
                        <p class="text-slate-400 text-xs mt-0.5">Atur jadwal mengajar secara fleksibel</p>
                    </div>
                </div>

                <div class="stat-card rounded-2xl px-5 py-4 flex items-center gap-4 animate-float-slow">
                    <div class="w-10 h-10 rounded-xl bg-violet-500/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm">Laporan Lengkap</p>
                        <p class="text-slate-400 text-xs mt-0.5">Export data kehadiran ke PDF/Excel</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom footer -->
        <div class="relative z-10">
            <p class="text-slate-500 text-xs">&copy; {{ date('Y') }} Sistem Informasi Monitoring Guru</p>
        </div>
    </div>

    <!-- ===================== RIGHT PANEL (Login Form) ===================== -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10 bg-white lg:bg-slate-50">
        <div class="w-full max-w-sm">

            <!-- Mobile logo -->
            <div class="flex lg:hidden items-center gap-3 mb-8 anim-1">
                <div class="w-10 h-10 rounded-xl bg-white p-1 flex items-center justify-center shadow-md">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <span class="text-navy-800 font-bold text-lg">Monitoring Guru</span>
            </div>

            <!-- Heading -->
            <div class="mb-8 anim-1">
                <h1 class="text-3xl font-bold text-slate-900">Selamat Datang 👋</h1>
                <p class="text-slate-500 mt-2 text-sm leading-relaxed">Masuk menggunakan Nomor Induk (NIP/NIK/NISN) dan kata sandi Anda.</p>
            </div>

            <!-- Error alert -->
            @if ($errors->any())
            <div class="mb-5 p-4 rounded-xl border border-red-200 bg-red-50 flex gap-3 anim-2">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-red-700 font-semibold text-sm">Login Gagal</p>
                    <p class="text-red-600 text-xs mt-0.5">{{ $errors->first() }}</p>
                </div>
            </div>
            @endif

            <!-- Session success -->
            @if (session('success'))
            <div class="mb-5 p-4 rounded-xl border border-emerald-200 bg-emerald-50 flex gap-3 anim-2">
                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-emerald-700 text-sm">{{ session('success') }}</p>
            </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <!-- NIK Field -->
                <div class="anim-2">
                    <label for="nik" class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor Induk (NIP / NIK / NISN)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4.5 h-4.5 text-slate-400" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                        </div>
                        <input type="text" id="nik" name="nik"
                            value="{{ old('nik') }}"
                            required autofocus
                            class="form-input w-full pl-10 pr-4 py-3 rounded-xl border text-sm text-slate-800 placeholder-slate-400 outline-none focus:border-brand-500 {{ $errors->has('nik') ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white' }}"
                            placeholder="Masukkan Nomor Induk Anda">
                    </div>
                    @error('nik')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="anim-3">
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Kata Sandi</label>
                    <div class="relative" x-data="{ show: false }">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="text-slate-400" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input :type="show ? 'text' : 'password'"
                            id="password" name="password" required
                            class="form-input w-full pl-10 pr-11 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 placeholder-slate-400 outline-none focus:border-brand-500"
                            placeholder="••••••••">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                            <svg x-show="!show" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" style="width:18px;height:18px;display:none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember me -->
                <div class="flex items-center justify-between anim-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input id="remember" name="remember" type="checkbox"
                            class="w-4 h-4 rounded border-slate-300 text-brand-500 focus:ring-brand-400 cursor-pointer">
                        <span class="text-sm text-slate-600">Ingat Saya</span>
                    </label>
                </div>

                <!-- Submit button -->
                <div class="anim-5">
                    <button type="submit" class="btn-submit w-full py-3.5 px-4 rounded-xl text-white font-semibold text-sm shadow-lg">
                        Masuk ke Dashboard
                    </button>
                </div>
            </form>

            <!-- Footer note -->
            <p class="mt-8 text-center text-xs text-slate-400 anim-5">
                Sistem Informasi Monitoring Guru &copy; {{ date('Y') }}
            </p>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>
