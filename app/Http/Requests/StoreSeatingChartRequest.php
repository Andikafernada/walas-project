<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSeatingChartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'layout' => ['required', 'array'],
            'layout.rows' => ['required', 'integer', 'min:1', 'max:20'],
            'layout.cols' => ['required', 'integer', 'min:1', 'max:20'],
            'effective_date' => ['required', 'date'],
            'expired_date' => ['nullable', 'date', 'after:effective_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama denah wajib diisi.',
            'layout.required' => 'Layout wajib diisi.',
            'layout.rows.required' => 'Jumlah baris wajib diisi.',
            'layout.rows.min' => 'Minimal 1 baris.',
            'layout.rows.max' => 'Maksimal 20 baris.',
            'layout.cols.required' => 'Jumlah kolom wajib diisi.',
            'layout.cols.min' => 'Minimal 1 kolom.',
            'layout.cols.max' => 'Maksimal 20 kolom.',
            'effective_date.required' => 'Tanggal berlaku wajib diisi.',
            'expired_date.after' => 'Tanggal kadaluarsa harus setelah tanggal berlaku.',
        ];
    }
}
