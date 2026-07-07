<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nisn' => ['nullable', 'string', 'max:20', 'unique:students,nisn'],
            'nis' => ['nullable', 'string', 'max:20', 'unique:students,nis'],
            'name' => ['required', 'string', 'max:100'],
            'gender' => ['nullable', 'in:laki-laki,perempuan'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'religion' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'mother_name' => ['nullable', 'string', 'max:100'],
            'parent_phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'parent_whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'emergency_contact' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png'],
            'poin' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama siswa wajib diisi.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'nis.unique' => 'NIS sudah terdaftar.',
            'birth_date.before' => 'Tanggal lahir harus sebelum hari ini.',
            'parent_whatsapp.regex' => 'Format nomor WhatsApp tidak valid.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
