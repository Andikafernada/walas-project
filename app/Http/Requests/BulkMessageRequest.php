<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:attendance,warning,announcement,report'],
            'message' => ['required', 'string', 'min:10', 'max:1000'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['exists:students,id'],
            'class_ids' => ['nullable', 'array'],
            'class_ids.*' => ['exists:classes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Tipe pesan wajib dipilih.',
            'message.required' => 'Isi pesan wajib diisi.',
            'message.min' => 'Pesan minimal 10 karakter.',
            'message.max' => 'Pesan maksimal 1000 karakter.',
            'student_ids.*.exists' => 'Beberapa siswa tidak ditemukan.',
            'class_ids.*.exists' => 'Beberapa kelas tidak ditemukan.',
        ];
    }
}
