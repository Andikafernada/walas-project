<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceSubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pin' => ['required', 'string', 'size:4', 'regex:/^[0-9]{4}$/'],
            'attendances' => ['required', 'array', 'min:1'],
            'attendances.*.student_id' => ['required', 'exists:students,id'],
            'attendances.*.status' => ['required', 'in:hadir,terlambat,sakit,izin,alpa'],
            'attendances.*.minutes_late' => ['nullable', 'integer', 'min:0'],
            'attendances.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'pin.required' => 'PIN wajib diisi.',
            'pin.size' => 'PIN harus 4 digit.',
            'pin.regex' => 'Format PIN tidak valid.',
            'attendances.required' => 'Data absensi wajib diisi.',
            'attendances.min' => 'Minimal 1 data absensi.',
            'attendances.*.student_id.required' => 'ID siswa wajib ada.',
            'attendances.*.student_id.exists' => 'Siswa tidak ditemukan.',
            'attendances.*.status.required' => 'Status absensi wajib dipilih.',
            'attendances.*.status.in' => 'Status tidak valid.',
        ];
    }
}
