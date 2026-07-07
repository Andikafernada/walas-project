<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Student;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\WaQueue;
use App\Models\Violation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get classes - DON'T use withCount since table name is 'classes'
        // Use manual count instead
        $classes = $user->classes()->get();

        // Manual count for students
        $classIds = $classes->pluck('id')->toArray();
        $totalStudents = count($classIds) > 0
            ? Student::whereIn('class_id', $classIds)->where('is_active', true)->count()
            : 0;

        // Stats
        $stats = [
            'total_classes' => $classes->count(),
            'total_students' => $totalStudents,
            'pending_messages' => $user->waQueues()->where('status', 'pending')->count(),
            'today_attendance' => $this->getTodayAttendanceStatus($user, $classes),
            'weekly_violations' => $this->getWeeklyViolations($user),
        ];

        // Recent activities with eager loading
        $recentAttendances = Attendance::where('user_id', $user->id)
            ->with(['student', 'student.classModel'])
            ->latest('date')
            ->limit(5)
            ->get();

        $recentViolations = Violation::where('user_id', $user->id)
            ->with('student')
            ->latest('date')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'classes',
            'stats',
            'recentAttendances',
            'recentViolations'
        ));
    }

    protected function getTodayAttendanceStatus($user, $classes)
    {
        $today = Carbon::today();
        $classIds = $classes->pluck('id');

        $totalSessions = AttendanceSession::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->whereIn('class_id', $classIds)
            ->count();

        $completedSessions = AttendanceSession::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->whereIn('class_id', $classIds)
            ->where('status', 'used')
            ->count();

        return [
            'total' => $totalSessions,
            'completed' => $completedSessions,
            'percentage' => $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100) : 0,
        ];
    }

    protected function getWeeklyViolations($user)
    {
        return Violation::where('user_id', $user->id)
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();
    }
}
