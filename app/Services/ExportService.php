<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\CashBook;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Violation;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ExportService
{
    /**
     * Export students to Excel.
     */
    public function exportStudents(ClassModel $class): \Maatwebsite\Excel\Excel
    {
        $students = $class->students()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return Excel::download(
            new class($students, $class) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings
            {
                private $students;
                private $class;

                public function __construct($students, $class)
                {
                    $this->students = $students;
                    $this->class = $class;
                }

                public function collection(): Collection
                {
                    return $this->students->map(function ($student) {
                        return [
                            $student->nisn ?? '',
                            $student->nis ?? '',
                            $student->name,
                            $student->gender === 'laki-laki' ? 'Laki-laki' : 'Perempuan',
                            $student->birth_date?->format('d/m/Y') ?? '',
                            $student->birth_place ?? '',
                            $student->religion ?? '',
                            $student->address ?? '',
                            $student->father_name ?? '',
                            $student->mother_name ?? '',
                            $student->parent_phone ?? '',
                            $student->parent_whatsapp ?? '',
                            $student->poin,
                        ];
                    });
                }

                public function headings(): array
                {
                    return [
                        'NISN',
                        'NIS',
                        'Nama Lengkap',
                        'Jenis Kelamin',
                        'Tanggal Lahir',
                        'Tempat Lahir',
                        'Agama',
                        'Alamat',
                        'Nama Ayah',
                        'Nama Ibu',
                        'No. Telepon',
                        'WhatsApp',
                        'Poin',
                    ];
                }
            },
            "siswa_{$class->name}_{$class->id}_" . date('Ymd') . ".xlsx"
        );
    }

    /**
     * Export attendance to Excel.
     */
    public function exportAttendance(ClassModel $class, Carbon $startDate, Carbon $endDate): \Maatwebsite\Excel\Excel
    {
        $sessions = $class->attendanceSessions()
            ->whereBetween('date', [$startDate, $endDate])
            ->with('attendances.student')
            ->orderBy('date')
            ->get();

        return Excel::download(
            new class($sessions, $class) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings
            {
                private $sessions;
                private $class;

                public function __construct($sessions, $class)
                {
                    $this->sessions = $sessions;
                    $this->class = $class;
                }

                public function collection(): Collection
                {
                    $rows = [];

                    foreach ($this->sessions as $session) {
                        foreach ($session->attendances as $attendance) {
                            $statusLabel = match($attendance->status) {
                                'hadir' => 'Hadir',
                                'terlambat' => 'Terlambat',
                                'sakit' => 'Sakit',
                                'izin' => 'Izin',
                                'alpa' => 'Alfa',
                                default => $attendance->status,
                            };

                            $rows[] = [
                                $session->date->format('d/m/Y'),
                                $attendance->student->name,
                                $attendance->student->nisn ?? '',
                                $statusLabel,
                                $attendance->minutes_late ?? '',
                                $attendance->notes ?? '',
                            ];
                        }
                    }

                    return collect($rows);
                }

                public function headings(): array
                {
                    return [
                        'Tanggal',
                        'Nama Siswa',
                        'NISN',
                        'Status',
                        'Menit Terlambat',
                        'Keterangan',
                    ];
                }
            },
            "absensi_{$class->name}_{$class->id}_" . date('Ymd') . ".xlsx"
        );
    }

    /**
     * Export attendance summary matrix.
     */
    public function exportAttendanceMatrix(ClassModel $class, Carbon $startDate, Carbon $endDate): \Maatwebsite\Excel\Excel
    {
        $students = $class->students()->where('is_active', true)->orderBy('name')->get();
        $sessions = $class->attendanceSessions()
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        return Excel::download(
            new class($students, $sessions) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\ShouldAutoSize
            {
                private $students;
                private $sessions;

                public function __construct($students, $sessions)
                {
                    $this->students = $students;
                    $this->sessions = $sessions;
                }

                public function collection(): Collection
                {
                    $rows = [];

                    foreach ($this->students as $student) {
                        $row = [
                            $student->name,
                            $student->nisn ?? '',
                        ];

                        foreach ($this->sessions as $session) {
                            $attendance = $session->attendances->firstWhere('student_id', $student->id);
                            $shortStatus = match($attendance?->status) {
                                'hadir' => 'H',
                                'terlambat' => 'T',
                                'sakit' => 'S',
                                'izin' => 'I',
                                'alpa' => 'A',
                                default => '-',
                            };
                            $row[] = $shortStatus;
                        }

                        $rows[] = $row;
                    }

                    return collect($rows);
                }

                public function headings(): array
                {
                    $header = ['Nama', 'NISN'];

                    foreach ($this->sessions as $session) {
                        $header[] = $session->date->format('d/m');
                    }

                    return $header;
                }
            },
            "matriks_absensi_{$class->name}_{$class->id}_" . date('Ymd') . ".xlsx"
        );
    }

    /**
     * Export violations to Excel.
     */
    public function exportViolations(ClassModel $class, Carbon $startDate, Carbon $endDate): \Maatwebsite\Excel\Excel
    {
        $violations = $class->violations()
            ->whereBetween('date', [$startDate, $endDate])
            ->with('student')
            ->orderBy('date', 'desc')
            ->get();

        return Excel::download(
            new class($violations) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings
            {
                private $violations;

                public function __construct($violations)
                {
                    $this->violations = $violations;
                }

                public function collection(): Collection
                {
                    return $this->violations->map(function ($v) {
                        return [
                            $v->date->format('d/m/Y'),
                            $v->student->name,
                            $v->student->nisn ?? '',
                            \App\Models\Violation::CATEGORIES[$v->category] ?? $v->category,
                            ucfirst($v->severity),
                            $v->description,
                            $v->poin_reduced,
                            $v->poin_after,
                        ];
                    });
                }

                public function headings(): array
                {
                    return [
                        'Tanggal',
                        'Nama Siswa',
                        'NISN',
                        'Kategori',
                        'Tingkat',
                        'Keterangan',
                        'Poin Dikurangi',
                        'Total Poin',
                    ];
                }
            },
            "pelanggaran_{$class->name}_{$class->id}_" . date('Ymd') . ".xlsx"
        );
    }

    /**
     * Export cash book to Excel.
     */
    public function exportCashBook(ClassModel $class, Carbon $startDate, Carbon $endDate): \Maatwebsite\Excel\Excel
    {
        $transactions = $class->cashBooks()
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        return Excel::download(
            new class($transactions) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings
            {
                private $transactions;

                public function __construct($transactions)
                {
                    $this->transactions = $transactions;
                }

                public function collection(): Collection
                {
                    return $this->transactions->map(function ($t) {
                        return [
                            $t->date->format('d/m/Y'),
                            $t->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                            $t->category,
                            $t->description,
                            $t->type === 'income' ? $t->amount : '',
                            $t->type === 'expense' ? $t->amount : '',
                            $t->created_by_name,
                        ];
                    });
                }

                public function headings(): array
                {
                    return [
                        'Tanggal',
                        'Tipe',
                        'Kategori',
                        'Keterangan',
                        'Pemasukan',
                        'Pengeluaran',
                        'Petugas',
                    ];
                }
            },
            "buku_kas_{$class->name}_{$class->id}_" . date('Ymd') . ".xlsx"
        );
    }

    /**
     * Export students template for import.
     */
    public function exportStudentTemplate(): \Maatwebsite\Excel\Excel
    {
        return Excel::download(
            new class implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings
            {
                public function collection(): Collection
                {
                    return collect([
                        [
                            '',
                            '',
                            'Contoh Nama',
                            'laki-laki',
                            '2008-01-15',
                            'Jakarta',
                            'Islam',
                            'Jl. Contoh No. 1',
                            'Bpk. Contoh',
                            'Ibu Contoh',
                            '081234567890',
                            '081234567890',
                        ],
                    ]);
                }

                public function headings(): array
                {
                    return [
                        'NISN',
                        'NIS',
                        'Nama Lengkap',
                        'Jenis Kelamin (laki-laki/perempuan)',
                        'Tanggal Lahir (YYYY-MM-DD)',
                        'Tempat Lahir',
                        'Agama',
                        'Alamat',
                        'Nama Ayah',
                        'Nama Ibu',
                        'No. Telepon',
                        'WhatsApp',
                    ];
                }
            },
            'template_import_siswa.xlsx'
        );
    }
}
