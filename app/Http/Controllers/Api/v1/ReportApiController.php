<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class ReportApiController extends Controller
{
    /**
     * Get student attendance report.
     */
    public function studentAttendance(Request $request, int $studentId): \Illuminate\Http\JsonResponse
    {
        $student = Student::with('classModel')->findOrFail($studentId);
        $user = $request->user();

        if ($student->classModel->user_id !== $user->id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $query = $student->attendances();

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $attendances = $query->get();

        $summary = [
            'total' => $attendances->count(),
            'hadir' => $attendances->where('status', 'hadir')->count(),
            'terlambat' => $attendances->where('status', 'terlambat')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'alpa' => $attendances->where('status', 'alpa')->count(),
        ];

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'nisn' => $student->nisn,
                'class' => $student->classModel->name,
            ],
            'summary' => $summary,
            'attendances' => $attendances->map(fn($a) => [
                'date' => $a->date->format('Y-m-d'),
                'status' => $a->status,
                'minutes_late' => $a->minutes_late,
            ]),
        ]);
    }

    /**
     * Get class attendance report.
     */
    public function classAttendance(Request $request, int $classId): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $class = \App\Models\ClassModel::findOrFail($classId);

        if ($class->user_id !== $user->id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $query = $class->attendances();

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $attendances = $query->with('student:id,name,nisn')->get();

        $summary = [
            'total' => $attendances->count(),
            'hadir' => $attendances->where('status', 'hadir')->count(),
            'terlambat' => $attendances->where('status', 'terlambat')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'alpa' => $attendances->where('status', 'alpa')->count(),
        ];

        return response()->json([
            'success' => true,
            'class' => [
                'id' => $class->id,
                'name' => $class->name,
            ],
            'summary' => $summary,
            'details' => $attendances->map(fn($a) => [
                'student' => $a->student->name,
                'date' => $a->date->format('Y-m-d'),
                'status' => $a->status,
            ]),
        ]);
    }

    /**
     * Get student violations report.
     */
    public function studentViolations(Request $request, int $studentId): \Illuminate\Http\JsonResponse
    {
        $student = Student::with('classModel')->findOrFail($studentId);
        $user = $request->user();

        if ($student->classModel->user_id !== $user->id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $query = $student->violations();

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $violations = $query->get();

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'nisn' => $student->nisn,
                'poin' => $student->poin,
            ],
            'summary' => [
                'total_violations' => $violations->count(),
                'total_poin_reduced' => $violations->sum('poin_reduced'),
            ],
            'violations' => $violations->map(fn($v) => [
                'date' => $v->date->format('Y-m-d'),
                'category' => $v->category,
                'description' => $v->description,
                'severity' => $v->severity,
                'poin_reduced' => $v->poin_reduced,
            ]),
        ]);
    }
}
