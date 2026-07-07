<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group 1. Attendance
 *
 * APIs for managing student attendance
 */
class AttendanceApiController extends Controller
{
    /**
     * List Attendances
     *
     * Get paginated list of attendances.
     *
     * @queryParam class_id Filter by class ID. Example: 1
     * @queryParam date_from Start date (Y-m-d). Example: 2024-01-01
     * @queryParam date_to End date (Y-m-d). Example: 2024-01-31
     * @queryParam status Filter by status. Example: hadir
     *
     * @response 200 {
     *   "success": true,
     *   "data": [...],
     *   "links": {...},
     *   "meta": {...}
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Attendance::whereHas('attendanceSession', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->with(['student:id,name,nisn', 'attendanceSession'])
            ->latest('date')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $attendances,
        ]);
    }

    /**
     * Submit Attendance
     *
     * Create or update attendance record.
     *
     * @bodyParam student_id int required Student ID. Example: 1
     * @bodyParam date date required Attendance date. Example: 2024-01-15
     * @bodyParam status string required Status: hadir, terlambat, sakit, izin, alpa. Example: hadir
     * @bodyParam minutes_late int optional Minutes late. Example: 15
     * @bodyParam notes string optional Notes. Example: Hadir tepat waktu
     *
     * @response 201 {
     *   "success": true,
     *   "data": {...}
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:hadir,terlambat,sakit,izin,alpa',
            'minutes_late' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $user = $request->user();

        if ($student->classModel->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 403);
        }

        $existing = Attendance::where('student_id', $validated['student_id'])
            ->whereDate('date', $validated['date'])
            ->first();

        if ($existing) {
            $existing->update($validated);
            $attendance = $existing;
        } else {
            $attendance = Attendance::create([
                ...$validated,
                'user_id' => $user->id,
                'class_id' => $student->class_id,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $attendance,
        ], $existing ? 200 : 201);
    }

    /**
     * Get Attendance Summary
     *
     * Get monthly attendance summary for all classes.
     *
     * @queryParam month Month in Y-m format. Example: 2024-01
     *
     * @response 200 {
     *   "success": true,
     *   "month": "2024-01",
     *   "data": [...]
     * }
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $month = $request->filled('month')
            ? \Carbon\Carbon::parse($request->month)
            : now();

        $classes = $user->classes()->withCount([
            'students',
            'attendances' => function ($query) use ($month) {
                $query->whereMonth('date', $month->month)
                    ->whereYear('date', $month->year);
            },
        ])->get();

        $summary = $classes->map(fn($class) => [
            'class_id' => $class->id,
            'class_name' => $class->name,
            'total_students' => $class->students_count,
            'attendances_count' => $class->attendances_count,
        ]);

        return response()->json([
            'success' => true,
            'month' => $month->format('Y-m'),
            'data' => $summary,
        ]);
    }
}
