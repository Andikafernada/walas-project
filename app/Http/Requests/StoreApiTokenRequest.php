<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApiTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'abilities' => ['required', 'array', 'min:1'],
            'abilities.*' => ['in:read,write,exam_monitor,attendance,cbt'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama token wajib diisi.',
            'abilities.required' => 'Minimal satu hak akses harus dipilih.',
            'abilities.*.in' => 'Hak akses tidak valid.',
            'expires_at.after' => 'Tanggal kadaluarsa harus di masa depan.',
        ];
    }
}
