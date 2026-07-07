<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\Violation;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function studentReport(Student $student)
    {
        $this->authorize('view', $student);

        $class = $student->classModel;

        $stats = [
            'total_attendances' => $student->attendances()->count(),
            'present' => $student->attendances()->where('status', 'hadir')->count(),
            'sick' => $student->attendances()->where('status', 'sakit')->count(),
            'permit' => $student->attendances()->where('status', 'izin')->count(),
            'absent' => $student->attendances()->where('status', 'alpa')->count(),
            'late' => $student->attendances()->where('status', 'terlambat')->count(),
            'total_violations' => $student->violations()->count(),
            'poin' => $student->poin,
        ];

        $recentAttendances = $student->attendances()
            ->latest('date')
            ->take(30)
            ->get();

        $recentViolations = $student->violations()
            ->latest('date')
            ->take(10)
            ->get();

        return view('dashboard.reports.student', compact('student', 'class', 'stats', 'recentAttendances', 'recentViolations'));
    }

    public function attendanceHistory(Student $student, Request $request)
    {
        $this->authorize('view', $student);

        $query = $student->attendances();

        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->whereMonth('date', $date->month)
                ->whereYear('date', $date->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->latest('date')->paginate(30);

        return view('dashboard.reports.attendance-history', compact('student', 'attendances'));
    }

    public function violationHistory(Student $student)
    {
        $this->authorize('view', $student);

        $violations = $student->violations()
            ->latest('date')
            ->paginate(30);

        return view('dashboard.reports.violation-history', compact('student', 'violations'));
    }

    public function attendanceReport(Request $request)
    {
        $classes = auth()->user()->classes()->with('students')->get();

        $query = auth()->user()->attendances();

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $attendances = $query->with(['student', 'student.classModel'])
            ->latest('date')
            ->paginate(50);

        return view('dashboard.reports.attendance', compact('classes', 'attendances'));
    }

    public function exportAttendance(Request $request)
    {
        // Export implementation
        return back()->with('info', 'Export dalam pengembangan.');
    }

    public function violationsReport(Request $request)
    {
        $classes = auth()->user()->classes()->get();

        $query = auth()->user()->violations();

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $violations = $query->with(['student', 'student.classModel'])
            ->latest('date')
            ->paginate(50);

        return view('dashboard.reports.violations', compact('classes', 'violations'));
    }

    public function cashFlowReport(Request $request)
    {
        $classes = auth()->user()->classes()->get();

        $query = auth()->user()->cashBooks();

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->latest('date')->paginate(50);

        $totals = [
            'income' => (clone $query)->where('type', 'income')->sum('amount'),
            'expense' => (clone $query)->where('type', 'expense')->sum('amount'),
        ];

        return view('dashboard.reports.cash-flow', compact('classes', 'transactions', 'totals'));
    }
}
