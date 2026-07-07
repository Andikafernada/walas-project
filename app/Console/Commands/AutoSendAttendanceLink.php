<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use App\Models\AttendanceSession;
use App\Models\OrganizationStructure;
use App\Models\WhatsAppSession;
use App\Models\WaQueue;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AutoSendAttendanceLink extends Command
{
    protected $signature = 'walas:auto-attendance';
    protected $description = 'Auto send attendance magic link based on schedule for ALL users';

    public function handle()
    {
        $this->info('Starting auto attendance link sender...');

        $now = Carbon::now();
        $today = strtolower($now->locale('id')->dayName);
        $currentTime = $now->format('H:i');
        $todayDate = $now->toDateString();

        // Get schedules that match current time
        $schedules = Schedule::where('day', $today)
            ->where('is_active', true)
            ->whereTime('start_time', '<=', $currentTime)
            ->with('classModel')
            ->get();

        $this->info("Checking {$schedules->count()} schedules for {$today} at {$currentTime}");

        $sent = 0;
        $skipped = 0;

        foreach ($schedules as $schedule) {
            $class = $schedule->classModel;
            $user = $class->user;

            if (!$user) {
                $this->warn("  No user for class: {$class->name}");
                $skipped++;
                continue;
            }

            // Check if user's WhatsApp is connected
            $waSession = WhatsAppSession::where('user_id', $user->id)
                ->where('status', 'connected')
                ->first();

            if (!$waSession) {
                $this->warn("  User {$user->name}: WhatsApp not connected");
                $skipped++;
                continue;
            }

            // Check if attendance already exists today for this class
            $existingSession = AttendanceSession::where('class_id', $class->id)
                ->whereDate('date', $todayDate)
                ->first();

            if ($existingSession) {
                $this->info("  Class {$class->name}: Already has attendance for today");
                $skipped++;
                continue;
            }

            // Create attendance session
            $attendanceSession = $this->createAttendanceSession($class, $user, $schedule);

            // Get Seksi Absensi
            $seksiAbsensi = $this->getSeksiAbsensi($class);

            if ($seksiAbsensi && $seksiAbsensi->parent_whatsapp) {
                // Queue WhatsApp message
                $this->sendAttendanceLink($user, $seksiAbsensi, $attendanceSession, $class);
                $this->info("  ✓ Sent to {$seksiAbsensi->student->name} for class {$class->name}");
                $sent++;
            } else {
                $this->warn("  No Seksi Absensi for class: {$class->name}");
            }
        }

        $this->info("Completed! Sent: {$sent}, Skipped: {$skipped}");
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
        $academicYear = now()->year . '-' . (now()->year + 1);

        return OrganizationStructure::where('class_id', $class->id)
            ->where(function ($q) {
                $q->where('position', 'seksi_kehadiran')
                  ->orWhere('position', 'seksi_absensi')
                  ->orWhere('position', 'seksi_attendance');
            })
            ->where('is_active', true)
            ->where('academic_year', $academicYear)
            ->with('student')
            ->first();
    }

    protected function sendAttendanceLink($user, $seksiAbsensi, $attendanceSession, $class)
    {
        $student = $seksiAbsensi->student;
        $magicLink = url('/absensi/' . $attendanceSession->token);

        $message = "📋 *LINK ABSENSI KELAS {$class->name}*\n\n";
        $message .= "Hai {$student->name}!\n";
        $message .= "Silakan isi absensi melalui tautan berikut:\n\n";
        $message .= "🔗 {$magicLink}\n\n";
        $message .= "🔑 PIN: *{$attendanceSession->pin}*\n\n";
        $message .= "⏰ Batas waktu: 15:00 WIB\n\n";
        $message .= "_Jangan sebarkan link ini!_";

        // Create queue entry
        WaQueue::create([
            'user_id' => $user->id,
            'student_id' => $seksiAbsensi->student_id,
            'phone' => $student->parent_whatsapp,
            'recipient_name' => $student->father_name ?? $student->name,
            'message' => $message,
            'type' => WaQueue::TYPE_ATTENDANCE,
            'status' => WaQueue::STATUS_PENDING,
        ]);
    }
}
