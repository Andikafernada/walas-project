<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'position' => ['required', 'in:ketua_kelas,wakil_ketua,sekretaris,bendahara,seksi_kehadiran,seksi_barang,seksi_kebersihan,seksi_keamanan,seksi_olahraga,seksi_kesenian'],
            'academic_year' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'Siswa wajib dipilih.',
            'student_id.exists' => 'Siswa tidak ditemukan.',
            'position.required' => 'Posisi wajib dipilih.',
            'position.in' => 'Posisi tidak valid.',
            'academic_year.required' => 'Tahun ajaran wajib diisi.',
            'academic_year.regex' => 'Format tahun ajaran tidak valid (contoh: 2024-2025).',
        ];
    }
}
