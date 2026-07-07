<?php

namespace Tests\Unit\Models;

use App\Models\AttendanceSession;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AttendanceSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_attendance_session(): void
    {
        $class = ClassModel::factory()->create();
        $session = AttendanceSession::factory()->forClass($class)->create();

        $this->assertDatabaseHas('attendance_sessions', [
            'id' => $session->id,
            'class_id' => $class->id,
        ]);
    }

    public function test_generate_token_creates_64_char_token(): void
    {
        $token = AttendanceSession::generateToken();

        $this->assertEquals(64, strlen($token));
        $this->assertTrue(Str::isUuid(preg_replace('/-/', '', $token)) || ctype_alnum($token));
    }

    public function test_generate_pin_creates_4_digit_pin(): void
    {
        $pin = AttendanceSession::generatePin();

        $this->assertEquals(4, strlen($pin));
        $this->assertMatchesRegularExpression('/^\d{4}$/', $pin);
    }

    public function test_magic_link_attribute(): void
    {
        $session = AttendanceSession::factory()->create([
            'token' => 'test-token-123',
        ]);

        $expectedLink = config('app.url') . '/absensi/test-token-123';

        $this->assertEquals($expectedLink, $session->magic_link);
    }

    public function test_is_expired_attribute_for_active_session(): void
    {
        $session = AttendanceSession::factory()->create([
            'status' => 'active',
            'expires_at' => now()->addHours(8),
        ]);

        $this->assertFalse($session->isExpired);
    }

    public function test_is_expired_attribute_for_expired_session(): void
    {
        $session = AttendanceSession::factory()->create([
            'status' => 'expired',
            'expires_at' => now()->subHours(1),
        ]);

        $this->assertTrue($session->isExpired);
    }

    public function test_remaining_minutes_attribute(): void
    {
        $session = AttendanceSession::factory()->create([
            'expires_at' => now()->addMinutes(30),
        ]);

        $this->assertGreaterThanOrEqual(29, $session->remaining_minutes);
        $this->assertLessThanOrEqual(31, $session->remaining_minutes);
    }

    public function test_belongs_to_class(): void
    {
        $class = ClassModel::factory()->create();
        $session = AttendanceSession::factory()->forClass($class)->create();

        $this->assertInstanceOf(ClassModel::class, $session->classModel);
        $this->assertEquals($class->id, $session->classModel->id);
    }

    public function test_has_many_attendances(): void
    {
        $class = ClassModel::factory()->create();
        $session = AttendanceSession::factory()->forClass($class)->create();
        $students = Student::factory()->count(3)->forClass($class)->create();

        foreach ($students as $student) {
            $session->attendances()->create([
                'student_id' => $student->id,
                'user_id' => $class->user_id,
                'class_id' => $class->id,
                'date' => $session->date,
                'status' => 'hadir',
            ]);
        }

        $this->assertCount(3, $session->attendances);
    }
}
