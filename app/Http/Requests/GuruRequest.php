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
            'name'               => 'required|string|max:100',
            'nik'                => [
                'required',
                'string',
                'max:50',
                'unique:users,nik,' . $guruId,
            ],
            'jenis_kelamin'      => 'required|string|in:Laki-laki,Perempuan',
            'no_telp'            => 'nullable|string|max:20',
            'status'             => 'required|string|in:Aktif,Cuti,Pensiun',
            'foto'               => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required'               => 'Nama lengkap wajib diisi.',
            'name.max'                    => 'Nama lengkap maksimal 100 karakter.',
            'nik.required'                => 'NIP/NIK wajib diisi.',
            'nik.string'                  => 'Format NIP/NIK tidak valid.',
            'nik.max'                     => 'NIP/NIK maksimal 50 karakter.',
            'nik.unique'                  => 'NIP/NIK sudah terdaftar di sistem.',
            'jenis_kelamin.required'      => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'            => 'Pilihan jenis kelamin tidak valid.',
            'status.required'             => 'Status aktif wajib diisi.',
            'status.in'                   => 'Status aktif tidak valid.',
            'foto.image'                  => 'File harus berupa gambar.',
            'foto.max'                    => 'Ukuran gambar maksimal adalah 2MB.',
            'foto.mimes'                  => 'Format gambar harus berupa JPG, JPEG, PNG, atau WEBP.',
        ];
    }
}
