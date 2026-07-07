<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use App\Models\ClassModel;
use App\Models\AttendanceSession;
use App\Models\OrganizationStructure;
use App\Models\WhatsAppSession;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AutoSendAttendanceLink extends Command
{
    protected $signature = 'walas:auto-attendance';
    protected $description = 'Auto send attendance magic link based on schedule';

    public function handle()
    {
        $this->info('Starting auto attendance link sender...');

        $now = Carbon::now();
        $today = strtolower($now->locale('id')->dayName);
        $currentTime = $now->format('H:i');

        // Get schedules for today
        $schedules = Schedule::where('day', $today)
            ->where('is_active', true)
            ->whereTime('start_time', '<=', $currentTime)
            ->whereTime('end_time', '>=', $currentTime)
            ->with('classModel')
            ->get();

        $this->info("Found {$schedules->count()} schedules for {$today} at {$currentTime}");

        foreach ($schedules as $schedule) {
            $class = $schedule->classModel;
            $user = $class->user;

            if (!$user) {
                $this->warn("No user for class: {$class->name}");
                continue;
            }

            // Check if user has WhatsApp connected
            $waSession = WhatsAppSession::where('user_id', $user->id)
                ->where('status', 'connected')
                ->first();

            if (!$waSession) {
                $this->warn("User {$user->name} has no WhatsApp connected");
                continue;
            }

            // Check if attendance session already exists for today
            $existingSession = AttendanceSession::where('class_id', $class->id)
                ->whereDate('date', $now->toDateString())
                ->first();

            if ($existingSession) {
                $this->info("Attendance already exists for class: {$class->name}");
                continue;
            }

            // Create attendance session
            $attendanceSession = $this->createAttendanceSession($class, $user, $schedule);

            // Get Seksi Absensi student
            $seksiAbsensi = $this->getSeksiAbsensi($class);

            if ($seksiAbsensi && $seksiAbsensi->parent_whatsapp) {
                // Send WhatsApp message
                $this->sendAttendanceLink($user, $seksiAbsensi, $attendanceSession);
                $this->info("Sent attendance link to Seksi Absensi for class: {$class->name}");
            } else {
                $this->warn("No Seksi Absensi found for class: {$class->name}");
            }
        }

        $this->info('Auto attendance link sender completed!');
    }

    protected function createAttendanceSession($class, $user, $schedule)
    {
        $today = Carbon::today();
        $expiresAt = $today->copy()->setTime(15, 0);

        return AttendanceSession::create([
            'class_id' => $class->id,
            'user_id' => $user->id,
            'date' => $today,
            'token' => bin2hex(random_bytes(16)),
            'pin' => str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT),
            'status' => 'active',
            'expires_at' => $expiresAt,
            'schedule_id' => $schedule->id,
        ]);
    }

    protected function getSeksiAbsensi($class)
    {
        return OrganizationStructure::where('class_id', $class->id)
            ->whereIn('position', ['seksi_kehadiran', 'seksi_absensi', 'seksi_attendance'])
            ->where('is_active', true)
            ->where('academic_year', now()->year . '-' . (now()->year + 1))
            ->with('student')
            ->first();
    }

    protected function sendAttendanceLink($user, $seksiAbsensi, $attendanceSession)
    {
        $class = $seksiAbsensi->classModel;
        $student = $seksiAbsensi->student;
        $magicLink = url('/absensi/' . $attendanceSession->token);

        $message = "📋 *LINK ABSENSI KELAS {$class->name}*\n\n";
        $message .= "Hai {$student->name}!\n";
        $message .= "Silakan isi absensi melalui tautan berikut:\n\n";
        $message .= "🔗 {$magicLink}\n\n";
        $message .= "🔑 PIN: *{$attendanceSession->pin}*\n\n";
        $message .= "⏰ Batas waktu: 15:00 WIB\n\n";
        $message .= "_Jangan sebarkan link ini!_";

        // Use WhatsApp queue
        \App\Models\WaQueue::create([
            'user_id' => $user->id,
            'student_id' => $seksiAbsensi->student_id,
            'phone' => $student->parent_whatsapp,
            'recipient_name' => $student->father_name ?? $student->name,
            'message' => $message,
            'type' => 'attendance',
            'status' => 'pending',
        ]);
    }
}
