<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuruRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $guruId = $this->route('guru'); // Get the guru route parameter (if any) for unique validation exclusion

        return [
            'name' => 'required|string|max:100',
            'nik' => [
                'required',
                'numeric',
                'digits:18',
                'unique:users,nik,' . $guruId,
            ],
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'no_telp' => 'nullable|string|max:20',
            'status_kepegawaian' => 'required|string|in:PNS,GTT,GTY,Honorer',
            'golongan' => 'required_if:status_kepegawaian,PNS|nullable|string|max:50',
            'tmt' => 'nullable|date',
            'status' => 'required|string|in:Aktif,Cuti,Pensiun',
            'mapel_id' => 'required|exists:mapels,id',
            'kelas_ids' => 'required|array|min:1',
            'kelas_ids.*' => 'exists:kelas,id',
            'jumlah_jam' => 'required|integer|min:0|max:48',
            'foto' => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 100 karakter.',
            'nik.required' => 'NIP wajib diisi.',
            'nik.numeric' => 'NIP harus berupa angka.',
            'nik.digits' => 'NIP harus tepat 18 digit.',
            'nik.unique' => 'NIP sudah terdaftar di sistem.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
            'status_kepegawaian.required' => 'Status kepegawaian wajib dipilih.',
            'status_kepegawaian.in' => 'Pilihan status kepegawaian tidak valid.',
            'golongan.required_if' => 'Golongan/Pangkat wajib diisi jika status kepegawaian adalah PNS.',
            'status.required' => 'Status aktif wajib diisi.',
            'status.in' => 'Status aktif tidak valid.',
            'mapel_id.required' => 'Mata pelajaran wajib dipilih.',
            'mapel_id.exists' => 'Mata pelajaran yang dipilih tidak terdaftar.',
            'kelas_ids.required' => 'Kelas pengampu wajib dipilih minimal satu.',
            'kelas_ids.array' => 'Kelas pengampu tidak valid.',
            'kelas_ids.*.exists' => 'Salah satu kelas pengampu tidak terdaftar.',
            'jumlah_jam.required' => 'Jumlah jam mengajar wajib diisi.',
            'jumlah_jam.integer' => 'Jumlah jam mengajar harus berupa angka.',
            'jumlah_jam.min' => 'Jumlah jam mengajar minimal 0 jam.',
            'jumlah_jam.max' => 'Jumlah jam mengajar maksimal 48 jam.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.max' => 'Ukuran gambar maksimal adalah 2MB.',
            'foto.mimes' => 'Format gambar harus berupa JPG, JPEG, PNG, atau WEBP.',
        ];
    }
}
