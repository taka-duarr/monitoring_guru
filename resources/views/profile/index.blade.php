@extends(Auth::user()->jabatan === 'admin' ? 'layouts.admin' : (Auth::user()->jabatan === 'guru' ? 'layouts.guru' : 'layouts.siswa'))

@section('title', 'Profil Saya - SIMGURU')

@section('content')
<div class="p-5 max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-primary-900">Profil Saya</h2>
            <p class="text-sm text-neutral-500">Kelola dan perbarui data profil akun Anda di bawah ini</p>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-success-50 border border-success-100 text-success-700 text-sm flex items-center gap-2 shadow-xs transition-all">
            <i class="ti ti-circle-check text-xl"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 rounded-xl bg-danger-50 border border-danger-100 text-danger-700 text-sm shadow-xs">
            <div class="font-semibold mb-1 flex items-center gap-2">
                <i class="ti ti-alert-circle text-xl"></i>
                Ada kesalahan pengisian form:
            </div>
            <ul class="list-disc pl-5 mt-1 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Grid Profile -->
    <form action="{{ route(Auth::user()->jabatan === 'admin' ? 'admin.profile.update' : (Auth::user()->jabatan === 'guru' ? 'guru.profile.update' : 'siswa.profile.update')) }}" method="POST" enctype="multipart/form-data" x-data="{
        loading: false,
        imagePreview: null,
        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.imagePreview = URL.createObjectURL(file);
            }
        }
    }" @submit="loading = true">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Left Side: Avatar Card (Read-only Info) -->
            <div class="md:col-span-1 flex flex-col gap-6">
                <div class="card p-6 rounded-2xl bg-white shadow-xs border border-neutral-300 text-center flex flex-col items-center justify-center relative overflow-hidden">
                    <!-- Background Decorative Pattern -->
                    <div class="absolute inset-x-0 top-0 h-24 bg-gradient-to-r from-primary-900 to-primary-700"></div>

                    <!-- Avatar Circle -->
                    <div class="relative mt-8 mb-4">
                        <!-- Preview Image if Selected -->
                        <img x-show="imagePreview" :src="imagePreview" alt="Avatar Preview" class="w-24 h-24 rounded-full border-4 border-white object-cover shadow-md" style="display: none;">

                        <!-- Original Image / Initials if No Preview Selected -->
                        <div x-show="!imagePreview">
                            @if($user->foto && file_exists(public_path('storage/' . $user->foto)))
                                <img src="{{ asset('storage/' . $user->foto) }}" alt="Avatar" class="w-24 h-24 rounded-full border-4 border-white object-cover shadow-md">
                            @else
                                @php
                                    $initials = collect(explode(' ', $user->name))->map(fn($n) => substr($n, 0, 1))->take(2)->join('');
                                @endphp
                                <div class="w-24 h-24 rounded-full border-4 border-white bg-primary-100 text-primary-800 text-2xl font-bold flex items-center justify-center shadow-md">
                                    {{ strtoupper($initials) }}
                                </div>
                            @endif
                        </div>

                        <!-- Edit Icon Overlay -->
                        <label for="foto-input" class="absolute bottom-0 right-0 bg-primary-600 hover:bg-primary-700 text-black rounded-full p-2 cursor-pointer shadow-md hover:scale-105 transition-all flex items-center justify-center w-8 h-8 border-2 border-white" title="Ubah Foto Profil">
                            <i class="ti ti-camera text-sm"></i>
                        </label>
                        <input type="file" name="foto" id="foto-input" class="hidden" accept="image/*" @change="previewImage">
                    </div>

                    <h3 class="font-bold text-lg text-neutral-800 leading-tight">{{ $user->name }}</h3>
                    <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider mt-1">{{ str_replace('_', ' ', $user->jabatan) }}</span>
                    <span class="text-[10px] text-neutral-400 mt-2">Klik ikon kamera untuk mengubah foto (Maks. 2MB)</span>
                    @error('foto')
                        <span class="text-xs text-danger mt-1 block font-semibold">{{ $message }}</span>
                    @enderror

                    <hr class="w-full border-neutral-100 my-4">

                    <!-- Simple Badges -->
                    <div class="flex flex-col gap-2 w-full text-left">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-neutral-400">NIK / ID</span>
                            <span class="font-bold text-neutral-700">{{ $user->nik }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-neutral-400">Status Akun</span>
                            <span class="badge badge-success rounded-full py-0.5 px-2.5 text-[10px] font-bold">{{ $user->status ?? 'Aktif' }}</span>
                        </div>
                        @if($user->kelas)
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-neutral-400">Kelas Diampu</span>
                                <span class="font-bold text-primary-700">{{ $user->kelas->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Side: Edit Form Card -->
            <div class="md:col-span-2">
                <div class="card p-6 rounded-2xl bg-white shadow-xs border border-neutral-300">
                    <h3 class="text-base font-bold text-primary-900 mb-4 pb-2 border-b border-neutral-100 flex items-center gap-2">
                        <i class="ti ti-user-cog text-lg"></i> Informasi Akun
                    </h3>

                    <!-- Grid fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIK (Disabled) -->
                        <div class="form-group">
                            <label class="form-label" for="nik" style="font-weight: 600; color: var(--color-neutral-600);">Nomor Induk Karyawan (NIK)</label>
                            <input type="text" id="nik" class="form-control bg-neutral-50 cursor-not-allowed opacity-75" value="{{ $user->nik }}" disabled>
                            <span class="text-[10px] text-neutral-400 mt-1 d-block">NIK tidak dapat diubah secara mandiri.</span>
                        </div>

                        <!-- Jabatan (Disabled) -->
                        <div class="form-group">
                            <label class="form-label" for="jabatan" style="font-weight: 600; color: var(--color-neutral-600);">Jabatan / Role</label>
                            <input type="text" id="jabatan" class="form-control bg-neutral-50 cursor-not-allowed opacity-75 capitalize" value="{{ str_replace('_', ' ', $user->jabatan) }}" disabled>
                            <span class="text-[10px] text-neutral-400 mt-1 d-block">Kontak Admin jika terdapat kesalahan peran.</span>
                        </div>

                        <!-- Nama Lengkap (Editable) -->
                        <div class="form-group md:col-span-2">
                            <label class="form-label" for="name" style="font-weight: 600; color: var(--color-neutral-600);">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="text-xs text-danger mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Nomor Telepon (Editable) -->
                        <div class="form-group">
                            <label class="form-label" for="no_telp" style="font-weight: 600; color: var(--color-neutral-600);">Nomor Telepon / WA</label>
                            <input type="text" name="no_telp" id="no_telp" class="form-control @error('no_telp') is-invalid @enderror" value="{{ old('no_telp', $user->no_telp) }}" placeholder="Contoh: 08123456789">
                            @error('no_telp')
                                <span class="text-xs text-danger mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Jenis Kelamin (Disabled) -->
                        <div class="form-group">
                            <label class="form-label" for="jenis_kelamin" style="font-weight: 600; color: var(--color-neutral-600);">Jenis Kelamin</label>
                            <input type="text" id="jenis_kelamin" class="form-control bg-neutral-50 cursor-not-allowed opacity-75" value="{{ $user->jenis_kelamin === 'L' ? 'Laki-Laki' : ($user->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}" disabled>
                        </div>
                    </div>

                    <h3 class="text-base font-bold text-primary-900 mt-6 mb-4 pb-2 border-b border-neutral-100 flex items-center gap-2">
                        <i class="ti ti-lock text-lg"></i> Perbarui Password
                    </h3>
                    <p class="text-xs text-neutral-400 mb-4">Kosongkan jika Anda tidak ingin mengubah password akun Anda saat ini.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Password Baru -->
                        <div class="form-group">
                            <label class="form-label" for="password" style="font-weight: 600; color: var(--color-neutral-600);">Password Baru</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Ketik password baru">
                            @error('password')
                                <span class="text-xs text-danger mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div class="form-group">
                            <label class="form-label" for="password_confirmation" style="font-weight: 600; color: var(--color-neutral-600);">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 pt-4 border-t border-neutral-100 flex justify-end">
                        <button type="submit" class="btn btn-primary flex items-center gap-2 px-6" :disabled="loading">
                            <template x-if="loading">
                                <span class="table-spinner" style="width: 14px; height: 14px; border-width: 2px; border-color: white; border-top-color: transparent;"></span>
                            </template>
                            <span x-text="loading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
