<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJournalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['required', 'in:konseling,kelompok,home_visit,call_parent,gurubk,admin'],
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'date' => ['required', 'date'],
            'student_id' => ['nullable', 'exists:students,id'],
            'outcome' => ['nullable', 'string', 'max:255'],
            'follow_up' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
        ];
    }

    public function messages(): array
    {
        return [
            'category.required' => 'Kategori journal wajib dipilih.',
            'subject.required' => 'Subjek wajib diisi.',
            'content.required' => 'Isi journal wajib diisi.',
            'date.required' => 'Tanggal wajib diisi.',
            'student_id.exists' => 'Siswa tidak ditemukan.',
        ];
    }
}
