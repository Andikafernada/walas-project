<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CashBookController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SeatingChartController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ViolationController;
use App\Http\Controllers\WaQueueController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PublicAttendanceController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ApiTokenController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('welcome'))->name('welcome');

/*
|--------------------------------------------------------------------------
| Demo Page
|--------------------------------------------------------------------------
*/
Route::get('/demo', fn() => view('welcome'))->name('demo');

/*
|--------------------------------------------------------------------------
| Magic Link Attendance (Public)
|--------------------------------------------------------------------------
*/
Route::get('/absensi/{token}', [PublicAttendanceController::class, 'show'])
    ->name('public.attendance.show');

Route::post('/absensi/{session}/submit', [PublicAttendanceController::class, 'submit'])
    ->name('public.attendance.submit');

/*
|--------------------------------------------------------------------------
| WhatsApp Bot Routes
|--------------------------------------------------------------------------
*/
Route::prefix('whatsapp-bot')->group(function () {
    Route::get('/', [App\Http\Controllers\WhatsAppBotController::class, 'index'])
        ->name('whatsapp-bot.index');
    Route::post('/generate-qr', [App\Http\Controllers\WhatsAppBotController::class, 'generateQr'])
        ->name('whatsapp-bot.generate-qr');
    Route::get('/status', [App\Http\Controllers\WhatsAppBotController::class, 'status'])
        ->name('whatsapp-bot.status');
    Route::post('/disconnect', [App\Http\Controllers\WhatsAppBotController::class, 'disconnect'])
        ->name('whatsapp-bot.disconnect');
    Route::post('/send-test', [App\Http\Controllers\WhatsAppBotController::class, 'sendTest'])
        ->name('whatsapp-bot.send-test');
});

/*
|--------------------------------------------------------------------------
| Google OAuth Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::get('/google', [App\Http\Controllers\Auth\GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/google/callback', [App\Http\Controllers\Auth\GoogleAuthController::class, 'callback'])->name('auth.google.callback');
    Route::get('/setup', [App\Http\Controllers\Auth\GoogleAuthController::class, 'setup'])->name('auth.setup');
    Route::post('/setup', [App\Http\Controllers\Auth\GoogleAuthController::class, 'completeRegistration'])->name('auth.setup.complete');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'activity'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Classes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['subscription'])->group(function () {

        Route::resource('classes', ClassController::class);

        // Class Students
        Route::prefix('classes/{class}')->group(function () {
            Route::get('/students', [StudentController::class, 'index'])
                ->name('classes.students.index');
            Route::post('/students', [StudentController::class, 'store'])
                ->name('classes.students.store');
            Route::get('/students/{student}/edit', [StudentController::class, 'edit'])
                ->name('classes.students.edit');
            Route::put('/students/{student}', [StudentController::class, 'update'])
                ->name('classes.students.update');
            Route::delete('/students/{student}', [StudentController::class, 'destroy'])
                ->name('classes.students.destroy');
            Route::post('/students/import', [StudentController::class, 'import'])
                ->name('classes.students.import');
            Route::get('/students/export', [StudentController::class, 'export'])
                ->name('classes.students.export');
        });

        // Class Schedules
        Route::prefix('classes/{class}/schedules')->group(function () {
            Route::get('/', [ScheduleController::class, 'index'])
                ->name('classes.schedules.index');
            Route::post('/', [ScheduleController::class, 'store'])
                ->name('classes.schedules.store');
            Route::put('/{schedule}', [ScheduleController::class, 'update'])
                ->name('classes.schedules.update');
            Route::delete('/{schedule}', [ScheduleController::class, 'destroy'])
                ->name('classes.schedules.destroy');
            Route::post('/import', [ScheduleController::class, 'import'])
                ->name('classes.schedules.import');
        });

        // Class Organization Structure
        Route::prefix('classes/{class}/organization')->group(function () {
            Route::get('/', [OrganizationController::class, 'index'])
                ->name('classes.organization.index');
            Route::post('/', [OrganizationController::class, 'store'])
                ->name('classes.organization.store');
            Route::put('/{structure}', [OrganizationController::class, 'update'])
                ->name('classes.organization.update');
            Route::delete('/{structure}', [OrganizationController::class, 'destroy'])
                ->name('classes.organization.destroy');
        });

        // Class Attendance
        Route::prefix('classes/{class}/attendance')->group(function () {
            Route::get('/', [AttendanceController::class, 'index'])
                ->name('classes.attendance.index');
            Route::post('/generate', [AttendanceController::class, 'generateLink'])
                ->name('classes.attendance.generate');
            Route::get('/{session}', [AttendanceController::class, 'show'])
                ->name('classes.attendance.show');
            Route::post('/{session}/resend', [AttendanceController::class, 'resendReminder'])
                ->name('classes.attendance.resend');
            Route::get('/export', [AttendanceController::class, 'export'])
                ->name('classes.attendance.export');
        });

        // Class Violations
        Route::prefix('classes/{class}/violations')->group(function () {
            Route::get('/', [ViolationController::class, 'index'])
                ->name('classes.violations.index');
            Route::post('/', [ViolationController::class, 'store'])
                ->name('classes.violations.store');
            Route::put('/{violation}', [ViolationController::class, 'update'])
                ->name('classes.violations.update');
            Route::delete('/{violation}', [ViolationController::class, 'destroy'])
                ->name('classes.violations.destroy');
        });

        // Class Cash Book
        Route::prefix('classes/{class}/cashbook')->group(function () {
            Route::get('/', [CashBookController::class, 'index'])
                ->name('classes.cashbook.index');
            Route::post('/', [CashBookController::class, 'store'])
                ->name('classes.cashbook.store');
            Route::put('/{cashBook}', [CashBookController::class, 'update'])
                ->name('classes.cashbook.update');
            Route::delete('/{cashBook}', [CashBookController::class, 'destroy'])
                ->name('classes.cashbook.destroy');
            Route::get('/export', [CashBookController::class, 'export'])
                ->name('classes.cashbook.export');
        });

        // Class Seating Charts
        Route::prefix('classes/{class}/seating-charts')->group(function () {
            Route::get('/', [SeatingChartController::class, 'index'])
                ->name('classes.seating-charts.index');
            Route::post('/', [SeatingChartController::class, 'store'])
                ->name('classes.seating-charts.store');
            Route::put('/{chart}', [SeatingChartController::class, 'update'])
                ->name('classes.seating-charts.update');
            Route::delete('/{chart}', [SeatingChartController::class, 'destroy'])
                ->name('classes.seating-charts.destroy');
            Route::get('/{chart}/print', [SeatingChartController::class, 'print'])
                ->name('classes.seating-charts.print');
        });

        // Class Journals
        Route::prefix('classes/{class}/journals')->group(function () {
            Route::get('/', [JournalController::class, 'index'])
                ->name('classes.journals.index');
            Route::post('/', [JournalController::class, 'store'])
                ->name('classes.journals.store');
            Route::put('/{journal}', [JournalController::class, 'update'])
                ->name('classes.journals.update');
            Route::delete('/{journal}', [JournalController::class, 'destroy'])
                ->name('classes.journals.destroy');
        });

        // Student Reports
        Route::prefix('students/{student}')->group(function () {
            Route::get('/report', [ReportController::class, 'studentReport'])
                ->name('students.report');
            Route::get('/attendance-history', [ReportController::class, 'attendanceHistory'])
                ->name('students.attendance-history');
            Route::get('/violations', [ReportController::class, 'violationHistory'])
                ->name('students.violations');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Reports (Global)
    |--------------------------------------------------------------------------
    */
    Route::prefix('reports')->group(function () {
        Route::get('/attendance', [ReportController::class, 'attendanceReport'])
            ->name('reports.attendance');
        Route::get('/attendance/export', [ReportController::class, 'exportAttendance'])
            ->name('reports.attendance.export');
        Route::get('/violations', [ReportController::class, 'violationsReport'])
            ->name('reports.violations');
        Route::get('/cash-flow', [ReportController::class, 'cashFlowReport'])
            ->name('reports.cash-flow');
    });

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Queue Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('wa-queue')->group(function () {
        Route::get('/', [WaQueueController::class, 'index'])
            ->name('wa-queue.index');
        Route::post('/{queue}/retry', [WaQueueController::class, 'retry'])
            ->name('wa-queue.retry');
        Route::delete('/{queue}', [WaQueueController::class, 'destroy'])
            ->name('wa-queue.destroy');
        Route::post('/bulk-send', [WaQueueController::class, 'bulkSend'])
            ->name('wa-queue.bulk-send');
    });

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])
            ->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])
            ->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])
            ->name('profile.destroy');
        Route::post('/phone/verify', [ProfileController::class, 'verifyPhone'])
            ->name('profile.phone.verify');
    });

    /*
    |--------------------------------------------------------------------------
    | Subscription Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('subscription')->group(function () {
        Route::get('/upgrade', [SubscriptionController::class, 'upgrade'])
            ->name('subscription.upgrade');
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])
            ->name('subscription.subscribe');
        Route::get('/billing', [SubscriptionController::class, 'billing'])
            ->name('subscription.billing');
    });

    /*
    |--------------------------------------------------------------------------
    | API Tokens (Pro Only)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['subscription:api_access'])->prefix('api-tokens')->group(function () {
        Route::get('/', [ApiTokenController::class, 'index'])
            ->name('api-tokens.index');
        Route::post('/', [ApiTokenController::class, 'store'])
            ->name('api-tokens.store');
        Route::delete('/{token}', [ApiTokenController::class, 'destroy'])
            ->name('api-tokens.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Super Admin Only)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::prefix('organizations')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\OrganizationController::class, 'index'])
            ->name('admin.organizations.index');
        Route::get('/{organization}', [App\Http\Controllers\Admin\OrganizationController::class, 'show'])
            ->name('admin.organizations.show');
        Route::patch('/{organization}/status', [App\Http\Controllers\Admin\OrganizationController::class, 'updateStatus'])
            ->name('admin.organizations.update-status');
    });
});

/*
|--------------------------------------------------------------------------
| Health Check
|--------------------------------------------------------------------------
*/
Route::get('/up', fn() => response()->json(['status' => 'ok']));
