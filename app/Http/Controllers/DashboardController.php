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
        $classes = $user->classes()->with('students')->get();

        // Stats
        $stats = [
            'total_classes' => $classes->count(),
            'total_students' => $classes->sum(fn($c) => $c->students()->where('is_active', true)->count()),
            'pending_messages' => $user->waQueues()->where('status', 'pending')->count(),
            'today_attendance' => $this->getTodayAttendanceStatus($user, $classes),
            'weekly_violations' => $this->getWeeklyViolations($user),
        ];

        // Recent activities
        $recentAttendances = Attendance::where('user_id', $user->id)
            ->with('student', 'student.classModel')
            ->latest('date')
            ->limit(5)
            ->get();

        $recentViolations = Violation::where('user_id', $user->id)
            ->with('student')
            ->latest('date')
            ->limit(5)
            ->get();

        // Today's classes
        $todayClasses = $classes->filter(function ($class) {
            $today = strtolower(Carbon::now()->locale('id')->dayName);
            return $class->schedules()->where('day', $today)->exists();
        });

        // Quick actions
        $quickActions = [
            [
                'name' => 'Buat Absensi',
                'icon' => 'clipboard-check',
                'route' => route('classes.index'),
                'description' => 'Generate magic link absensi',
            ],
            [
                'name' => 'Tambah Siswa',
                'icon' => 'user-plus',
                'route' => route('classes.index'),
                'description' => 'Registrasi siswa baru',
            ],
            [
                'name' => 'Catat Pelanggaran',
                'icon' => 'exclamation-circle',
                'route' => route('classes.index'),
                'description' => 'Input poin pelanggaran',
            ],
            [
                'name' => 'Lihat Laporan',
                'icon' => 'document-chart-bar',
                'route' => route('reports.attendance'),
                'description' => 'Summary laporan',
            ],
        ];

        return view('dashboard.index', compact(
            'classes',
            'stats',
            'recentAttendances',
            'recentViolations',
            'todayClasses',
            'quickActions'
        ));
    }

    protected function getTodayAttendanceStatus($user, $classes)
    {
        $today = Carbon::today();

        $totalSessions = AttendanceSession::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->whereIn('class_id', $classes->pluck('id'))
            ->count();

        $completedSessions = AttendanceSession::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->whereIn('class_id', $classes->pluck('id'))
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
