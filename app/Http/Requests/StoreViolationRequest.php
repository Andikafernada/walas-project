<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreViolationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'category' => ['required', 'in:terlambat,tidak_mengerjakan_tugas,mengganggu_teman,merokok,bolos,tidak_uniform,hp_di_kelas,tidak_sopan,lainnya'],
            'description' => ['required', 'string', 'max:500'],
            'severity' => ['required', 'in:ringan,sedang,berat'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'attachment' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'Siswa wajib dipilih.',
            'student_id.exists' => 'Siswa tidak ditemukan.',
            'category.required' => 'Kategori pelanggaran wajib dipilih.',
            'description.required' => 'Deskripsi pelanggaran wajib diisi.',
            'severity.required' => 'Tingkat pelanggaran wajib dipilih.',
            'date.required' => 'Tanggal pelanggaran wajib diisi.',
            'date.before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini.',
            'attachment.max' => 'Lampiran maksimal 2MB.',
        ];
    }
}
