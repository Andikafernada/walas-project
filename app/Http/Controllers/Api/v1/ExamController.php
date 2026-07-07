<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ClassModel;
use App\Models\ExamSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group 3. Exam Monitoring
 *
 * APIs for CBT/ExamBrowser integration to monitor exam sessions
 */
class ExamController extends Controller
{
    /**
     * Start Exam Session
     *
     * Initialize exam monitoring session.
     *
     * @bodyParam session_id string required External exam session ID. Example: "EXAM-2024-001"
     * @bodyParam exam_name string required Exam name. Example: "UAS Matematika"
     * @bodyParam class_id int required Class ID. Example: 1
     * @bodyParam start_time datetime required Exam start time. Example: "2024-01-15T08:00:00Z"
     * @bodyParam end_time datetime optional Exam end time. Example: "2024-01-15T10:00:00Z"
     *
     * @response 201 {
     *   "success": true,
     *   "session_id": 1,
     *   "students": [...]
     * }
     */
    public function start(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => 'required|string',
            'exam_name' => 'required|string',
            'class_id' => 'required|exists:classes,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
        ]);

        $user = $request->user();
        $class = ClassModel::findOrFail($validated['class_id']);

        if ($class->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $examSession = ExamSession::create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'external_session_id' => $validated['session_id'],
            'exam_name' => $validated['exam_name'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'status' => 'active',
        ]);

        $students = $class->students()
            ->where('is_active', true)
            ->select(['id', 'name', 'nisn'])
            ->get();

        return response()->json([
            'success' => true,
            'session_id' => $examSession->id,
            'students' => $students,
        ], 201);
    }

    /**
     * Log Exam Event
     *
     * Log student activity during exam.
     *
     * @bodyParam session_id int required Internal session ID. Example: 1
     * @bodyParam student_id int required Student ID. Example: 1
     * @bodyParam event string required Event type: start, tab_switch, focus_lost, focus_gained, suspicious, end. Example: tab_switch
     * @bodyParam details string optional Event details. Example: "Switched to Telegram"
     * @bodyParam timestamp datetime optional Event timestamp. Example: "2024-01-15T08:30:00Z"
     *
     * @response 200 {
     *   "success": true
     * }
     */
    public function log(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:exam_sessions,id',
            'student_id' => 'required|exists:students,id',
            'event' => 'required|in:start,tab_switch,focus_lost,focus_gained,suspicious,end',
            'details' => 'nullable|string',
            'timestamp' => 'nullable|date',
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'exam_event',
            'model_type' => ExamSession::class,
            'model_id' => $validated['session_id'],
            'new_values' => [
                'student_id' => $validated['student_id'],
                'event' => $validated['event'],
                'details' => $validated['details'],
                'timestamp' => $validated['timestamp'] ?? now()->toIso8601String(),
            ],
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * End Exam Session
     *
     * Mark exam session as ended.
     *
     * @bodyParam session_id int required Session ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "summary": {"total_events": 45}
     * }
     */
    public function end(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:exam_sessions,id',
        ]);

        $examSession = ExamSession::findOrFail($validated['session_id']);

        if ($examSession->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $examSession->update([
            'status' => 'ended',
            'end_time' => now(),
        ]);

        return response()->json([
            'success' => true,
            'summary' => [
                'total_events' => ActivityLog::where('model_type', ExamSession::class)
                    ->where('model_id', $examSession->id)
                    ->count(),
            ],
        ]);
    }

    /**
     * Get Exam Status
     *
     * Get current exam session status.
     *
     * @pathParam sessionId string Session ID (internal or external). Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "exam_name": "UAS Matematika",
     *     "status": "active",
     *     "event_count": 45
     *   }
     * }
     */
    public function status(Request $request, string $sessionId): JsonResponse
    {
        $examSession = ExamSession::where('external_session_id', $sessionId)
            ->orWhere('id', $sessionId)
            ->firstOrFail();

        if ($examSession->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $examSession->id,
                'exam_name' => $examSession->exam_name,
                'status' => $examSession->status,
                'start_time' => $examSession->start_time,
                'end_time' => $examSession->end_time,
                'event_count' => ActivityLog::where('model_type', ExamSession::class)
                    ->where('model_id', $examSession->id)
                    ->count(),
            ],
        ]);
    }
}
