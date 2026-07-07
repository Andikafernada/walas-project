<?php

use App\Http\Controllers\Api\v1\AttendanceApiController;
use App\Http\Controllers\Api\v1\StudentApiController;
use App\Http\Controllers\Api\v1\ExamController;
use App\Http\Controllers\Api\v1\ReportApiController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - WaliKelas Pro API
|--------------------------------------------------------------------------
| External integrations: CBT Systems, ExamBrowser, School Management
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware(['api.token'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Exam Monitoring API
    |--------------------------------------------------------------------------
    | Used by CBT/ExamBrowser for exam session monitoring
    |--------------------------------------------------------------------------
    */
    Route::prefix('exam')->group(function () {
        // Start exam session monitoring
        Route::post('/start', [ExamController::class, 'start'])
            ->name('api.v1.exam.start');

        // Log student activity during exam
        Route::post('/log', [ExamController::class, 'log'])
            ->name('api.v1.exam.log');

        // End exam session
        Route::post('/end', [ExamController::class, 'end'])
            ->name('api.v1.exam.end');

        // Get exam status
        Route::get('/status/{sessionId}', [ExamController::class, 'status'])
            ->name('api.v1.exam.status');
    });

    /*
    |--------------------------------------------------------------------------
    | Student Data API
    |--------------------------------------------------------------------------
    | Retrieve student information for CBT integration
    |--------------------------------------------------------------------------
    */
    Route::prefix('students')->group(function () {
        // List students by class
        Route::get('/class/{classId}', [StudentApiController::class, 'byClass'])
            ->name('api.v1.students.byClass');

        // Get student details
        Route::get('/{studentId}', [StudentApiController::class, 'show'])
            ->name('api.v1.students.show');

        // Get student photo
        Route::get('/{studentId}/photo', [StudentApiController::class, 'photo'])
            ->name('api.v1.students.photo');
    });

    /*
    |--------------------------------------------------------------------------
    | Attendance API
    |--------------------------------------------------------------------------
    | For school-wide attendance systems
    |--------------------------------------------------------------------------
    */
    Route::prefix('attendance')->group(function () {
        // Submit attendance
        Route::post('/', [AttendanceApiController::class, 'store'])
            ->name('api.v1.attendance.store');

        // Get attendance by date/class
        Route::get('/', [AttendanceApiController::class, 'index'])
            ->name('api.v1.attendance.index');

        // Get attendance summary
        Route::get('/summary', [AttendanceApiController::class, 'summary'])
            ->name('api.v1.attendance.summary');
    });

    /*
    |--------------------------------------------------------------------------
    | Reports API
    |--------------------------------------------------------------------------
    */
    Route::prefix('reports')->group(function () {
        // Student attendance report
        Route::get('/student/{studentId}/attendance', [ReportApiController::class, 'studentAttendance'])
            ->name('api.v1.reports.studentAttendance');

        // Class attendance report
        Route::get('/class/{classId}/attendance', [ReportApiController::class, 'classAttendance'])
            ->name('api.v1.reports.classAttendance');

        // Violation report
        Route::get('/student/{studentId}/violations', [ReportApiController::class, 'studentViolations'])
            ->name('api.v1.reports.studentViolations');
    });
});

/*
|--------------------------------------------------------------------------
| Webhook Routes (for N8N integrations)
|--------------------------------------------------------------------------
*/
Route::prefix('webhook')->group(function () {
    // WhatsApp incoming webhook
    Route::post('/whatsapp/incoming', [WebhookController::class, 'whatsappIncoming'])
        ->name('webhook.whatsapp.incoming');

    // WhatsApp send status callback
    Route::post('/whatsapp/status', [WebhookController::class, 'whatsappStatus'])
        ->name('webhook.whatsapp.status');

    // N8N automation triggers
    Route::post('/n8n/attendance', [WebhookController::class, 'n8nAttendance'])
        ->name('webhook.n8n.attendance');

    Route::post('/n8n/notification', [WebhookController::class, 'n8nNotification'])
        ->name('webhook.n8n.notification');
});
