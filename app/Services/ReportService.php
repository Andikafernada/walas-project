<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\CashBook;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Violation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get attendance report for a class.
     */
    public function getAttendanceReport(ClassModel $class, Carbon $startDate, Carbon $endDate): array
    {
        $attendances = Attendance::where('class_id', $class->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('student')
            ->get();

        $summary = $this->calculateAttendanceSummary($attendances);
        $dailyBreakdown = $this->getDailyAttendanceBreakdown($attendances, $startDate, $endDate);
        $studentBreakdown = $this->getStudentAttendanceBreakdown($attendances);

        return [
            'class' => [
                'id' => $class->id,
                'name' => $class->name,
            ],
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'days' => $startDate->diffInDays($endDate) + 1,
            ],
            'summary' => $summary,
            'daily_breakdown' => $dailyBreakdown,
            'student_breakdown' => $studentBreakdown,
        ];
    }

    /**
     * Get violation report for a class.
     */
    public function getViolationReport(ClassModel $class, Carbon $startDate, Carbon $endDate): array
    {
        $violations = Violation::where('class_id', $class->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('student')
            ->get();

        $summary = $this->calculateViolationSummary($violations);
        $byCategory = $violations->groupBy('category')->map->count();
        $bySeverity = $violations->groupBy('severity')->map->count();
        $topViolators = $this->getTopViolators($violations);

        return [
            'class' => [
                'id' => $class->id,
                'name' => $class->name,
            ],
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'summary' => $summary,
            'by_category' => $byCategory,
            'by_severity' => $bySeverity,
            'top_violators' => $topViolators,
        ];
    }

    /**
     * Get cash flow report for a class.
     */
    public function getCashFlowReport(ClassModel $class, Carbon $startDate, Carbon $endDate): array
    {
        $transactions = CashBook::where('class_id', $class->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;

        $byCategory = $transactions->groupBy('category')
            ->map(fn($group) => [
                'income' => $group->where('type', 'income')->sum('amount'),
                'expense' => $group->where('type', 'expense')->sum('amount'),
            ]);

        $monthlyBreakdown = $transactions
            ->groupBy(fn($t) => $t->date->format('Y-m'))
            ->map(fn($group) => [
                'income' => $group->where('type', 'income')->sum('amount'),
                'expense' => $group->where('type', 'expense')->sum('amount'),
            ]);

        return [
            'class' => [
                'id' => $class->id,
                'name' => $class->name,
            ],
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'income' => $income,
                'expense' => $expense,
                'balance' => $balance,
                'transaction_count' => $transactions->count(),
            ],
            'by_category' => $byCategory,
            'monthly_breakdown' => $monthlyBreakdown,
        ];
    }

    /**
     * Get student comprehensive report.
     */
    public function getStudentReport(Student $student, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        $attendances = $student->attendances()
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $violations = $student->violations()
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $attendanceSummary = $this->calculateAttendanceSummary($attendances);
        $violationSummary = $this->calculateViolationSummary($violations);

        return [
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'nisn' => $student->nisn,
                'class' => $student->classModel->name,
                'current_poin' => $student->poin,
            ],
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'attendance' => $attendanceSummary,
            'violations' => $violationSummary,
            'recent_attendances' => $student->attendances()
                ->orderBy('date', 'desc')
                ->limit(10)
                ->get(),
            'recent_violations' => $student->violations()
                ->orderBy('date', 'desc')
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * Get monthly summary for all classes.
     */
    public function getMonthlySummary(int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $classes = auth()->user()->classes()->with('students')->get();

        return $classes->map(function ($class) use ($startDate, $endDate) {
            $attendances = Attendance::where('class_id', $class->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $summary = $this->calculateAttendanceSummary($attendances);

            return [
                'class_id' => $class->id,
                'class_name' => $class->name,
                'student_count' => $class->students()->where('is_active', true)->count(),
                'attendance' => $summary,
            ];
        })->toArray();
    }

    /**
     * Calculate attendance summary.
     */
    protected function calculateAttendanceSummary($attendances): array
    {
        $total = $attendances->count();

        return [
            'total' => $total,
            'hadir' => $attendances->where('status', 'hadir')->count(),
            'terlambat' => $attendances->where('status', 'terlambat')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'alpa' => $attendances->where('status', 'alpa')->count(),
            'attendance_rate' => $total > 0 ? round((($attendances->whereIn('status', ['hadir', 'terlambat'])->count()) / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Calculate violation summary.
     */
    protected function calculateViolationSummary($violations): array
    {
        return [
            'total' => $violations->count(),
            'total_poin_reduced' => $violations->sum('poin_reduced'),
            'ringan' => $violations->where('severity', 'ringan')->count(),
            'sedang' => $violations->where('severity', 'sedang')->count(),
            'berat' => $violations->where('severity', 'berat')->count(),
        ];
    }

    /**
     * Get daily attendance breakdown.
     */
    protected function getDailyAttendanceBreakdown($attendances, Carbon $startDate, Carbon $endDate): array
    {
        $breakdown = [];

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dayAttendances = $attendances->where('date', $date->format('Y-m-d'));

            $breakdown[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->locale('id')->dayName,
                'total' => $dayAttendances->count(),
                'hadir' => $dayAttendances->where('status', 'hadir')->count(),
                'terlambat' => $dayAttendances->where('status', 'terlambat')->count(),
                'sakit' => $dayAttendances->where('status', 'sakit')->count(),
                'izin' => $dayAttendances->where('status', 'izin')->count(),
                'alpa' => $dayAttendances->where('status', 'alpa')->count(),
            ];
        }

        return $breakdown;
    }

    /**
     * Get student attendance breakdown.
     */
    protected function getStudentAttendanceBreakdown($attendances): array
    {
        return $attendances->groupBy('student_id')
            ->map(fn($group) => [
                'student_id' => $group->first()->student_id,
                'student_name' => $group->first()->student->name ?? 'Unknown',
                'total' => $group->count(),
                'hadir' => $group->where('status', 'hadir')->count(),
                'terlambat' => $group->where('status', 'terlambat')->count(),
                'sakit' => $group->where('status', 'sakit')->count(),
                'izin' => $group->where('status', 'izin')->count(),
                'alpa' => $group->where('status', 'alpa')->count(),
            ])
            ->values()
            ->toArray();
    }

    /**
     * Get top violators.
     */
    protected function getTopViolators($violations): array
    {
        return $violations->groupBy('student_id')
            ->map(fn($group, $studentId) => [
                'student_id' => $studentId,
                'student_name' => $group->first()->student->name ?? 'Unknown',
                'violation_count' => $group->count(),
                'total_poin' => $group->sum('poin_reduced'),
            ])
            ->sortByDesc('total_poin')
            ->take(10)
            ->values()
            ->toArray();
    }
}
