<?php

namespace Tests\Unit\Services;

use App\Helpers\AttendanceHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceHelperTest extends TestCase
{
    public function test_status_color(): void
    {
        $this->assertEquals('green', AttendanceHelper::statusColor('hadir'));
        $this->assertEquals('yellow', AttendanceHelper::statusColor('terlambat'));
        $this->assertEquals('blue', AttendanceHelper::statusColor('sakit'));
        $this->assertEquals('purple', AttendanceHelper::statusColor('izin'));
        $this->assertEquals('red', AttendanceHelper::statusColor('alpa'));
        $this->assertEquals('gray', AttendanceHelper::statusColor('unknown'));
    }

    public function test_status_label(): void
    {
        $this->assertEquals('Hadir', AttendanceHelper::statusLabel('hadir'));
        $this->assertEquals('Terlambat', AttendanceHelper::statusLabel('terlambat'));
        $this->assertEquals('Sakit', AttendanceHelper::statusLabel('sakit'));
        $this->assertEquals('Izin', AttendanceHelper::statusLabel('izin'));
        $this->assertEquals('Alfa', AttendanceHelper::statusLabel('alpa'));
        $this->assertEquals('Aktif', AttendanceHelper::statusLabel('active'));
    }

    public function test_short_status(): void
    {
        $this->assertEquals('H', AttendanceHelper::shortStatus('hadir'));
        $this->assertEquals('T', AttendanceHelper::shortStatus('terlambat'));
        $this->assertEquals('S', AttendanceHelper::shortStatus('sakit'));
        $this->assertEquals('I', AttendanceHelper::shortStatus('izin'));
        $this->assertEquals('A', AttendanceHelper::shortStatus('alpa'));
        $this->assertEquals('-', AttendanceHelper::shortStatus('unknown'));
    }

    public function test_attendance_rate(): void
    {
        $this->assertEquals(80.0, AttendanceHelper::attendanceRate(8, 10));
        $this->assertEquals(100.0, AttendanceHelper::attendanceRate(10, 10));
        $this->assertEquals(0.0, AttendanceHelper::attendanceRate(0, 0));
    }

    public function test_status_emoji(): void
    {
        $this->assertEquals('✅', AttendanceHelper::statusEmoji('hadir'));
        $this->assertEquals('⏰', AttendanceHelper::statusEmoji('terlambat'));
        $this->assertEquals('🏥', AttendanceHelper::statusEmoji('sakit'));
        $this->assertEquals('📝', AttendanceHelper::statusEmoji('izin'));
        $this->assertEquals('❌', AttendanceHelper::statusEmoji('alpa'));
    }

    public function test_session_status_color(): void
    {
        $this->assertEquals('yellow', AttendanceHelper::sessionStatusColor('active'));
        $this->assertEquals('green', AttendanceHelper::sessionStatusColor('used'));
        $this->assertEquals('gray', AttendanceHelper::sessionStatusColor('expired'));
    }

    public function test_format_late(): void
    {
        $this->assertEquals('-', AttendanceHelper::formatLate(0));
        $this->assertEquals('30 menit', AttendanceHelper::formatLate(30));
        $this->assertEquals('1 jam', AttendanceHelper::formatLate(60));
        $this->assertEquals('1 jam 30 menit', AttendanceHelper::formatLate(90));
    }
}
