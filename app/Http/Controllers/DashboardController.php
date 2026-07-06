<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Student;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\WaQueue;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $classes = $user->classes()->with('students')->get();

        $stats = [
            'total_classes' => $classes->count(),
            'total_students' => $classes->sum(fn($c) => $c->students()->where('is_active', true)->count()),
            'pending_messages' => $user->waQueues()->where('status', 'pending')->count(),
            'today_attendance_done' => AttendanceSession::where('user_id', $user->id)
                ->whereDate('date', Carbon::today())
                ->whereIn('class_id', $classes->pluck('id'))
                ->exists(),
        ];

        return view('dashboard.index', compact('classes', 'stats'));
    }
}
