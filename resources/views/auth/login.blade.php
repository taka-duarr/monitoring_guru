<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Monitoring Guru</title>
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
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
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
<body class="bg-pattern min-h-screen flex items-center justify-center font-sans text-slate-800 antialiased p-4 relative overflow-hidden">

    <!-- Decorative Blobs -->
    <div class="absolute top-10 left-10 w-72 h-72 bg-brand-400/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
    <div class="absolute bottom-10 right-10 w-72 h-72 bg-emerald-400/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>

    <div class="glass-panel w-full max-w-md rounded-3xl shadow-2xl p-8 relative z-10">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-brand-100 text-brand-600 rounded-2xl mx-auto flex items-center justify-center mb-4 shadow-sm border border-brand-200">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h1 class="font-heading text-3xl font-bold text-slate-800">Selamat Datang</h1>
            <p class="text-slate-500 mt-2">Silakan masuk menggunakan NIK Anda.</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="nik" class="block text-sm font-medium text-slate-700 mb-1">Nomor Induk Kependudukan (NIK)</label>
                <input type="text" id="nik" name="nik" value="{{ old('nik') }}" required autofocus
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors shadow-sm @error('nik') border-red-500 @enderror"
                    placeholder="Masukkan NIK">
                @error('nik')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Kata Sandi</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors shadow-sm"
                    placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-slate-600">
                        Ingat Saya
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all duration-300 transform hover:-translate-y-0.5">
                Masuk ke Dashboard
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="/" class="text-sm text-slate-500 hover:text-brand-600 font-medium transition-colors flex items-center justify-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>

</body>
</html>
