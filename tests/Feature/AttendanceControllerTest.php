<?php

namespace Tests\Feature;

use App\Models\AttendanceSession;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected ClassModel $class;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->class = ClassModel::factory()->forUser($this->user)->create();
    }

    public function test_user_can_view_attendance_list(): void
    {
        AttendanceSession::factory()->count(3)->forClass($this->class)->create();

        $response = $this->actingAs($this->user)
            ->get(route('classes.attendance.index', $this->class));

        $response->assertStatus(200);
        $response->assertSee('Absensi');
    }

    public function test_user_can_generate_magic_link(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('classes.attendance.generate', $this->class)
        );

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendance_sessions', [
            'class_id' => $this->class->id,
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);
    }

    public function test_generated_link_has_token_and_pin(): void
    {
        $this->actingAs($this->user)->post(
            route('classes.attendance.generate', $this->class)
        );

        $session = AttendanceSession::where('class_id', $this->class->id)->first();

        $this->assertNotNull($session->token);
        $this->assertNotNull($session->pin);
        $this->assertEquals(64, strlen($session->token));
        $this->assertEquals(4, strlen($session->pin));
    }

    public function test_magic_link_form_accessible(): void
    {
        $session = AttendanceSession::factory()->forClass($this->class)->create([
            'status' => 'active',
            'expires_at' => now()->addHours(8),
        ]);

        $response = $this->get(route('public.attendance.show', $session->token));

        $response->assertStatus(200);
    }

    public function test_expired_session_shows_expired_page(): void
    {
        $session = AttendanceSession::factory()->forClass($this->class)->create([
            'status' => 'active',
            'expires_at' => now()->subHours(1),
        ]);

        $response = $this->get(route('public.attendance.show', $session->token));

        $response->assertStatus(200);
        $response->assertSee('kedaluwarsa');
    }

    public function test_used_session_shows_used_page(): void
    {
        $session = AttendanceSession::factory()->forClass($this->class)->create([
            'status' => 'used',
        ]);

        $response = $this->get(route('public.attendance.show', $session->token));

        $response->assertStatus(200);
        $response->assertSee('sudah submitted');
    }

    public function test_user_cannot_generate_duplicate_today_session(): void
    {
        // Create existing session for today
        AttendanceSession::factory()->forClass($this->class)->create([
            'date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->post(
            route('classes.attendance.generate', $this->class)
        );

        $response->assertSessionHas('info');

        // Should still only have one session for today
        $this->assertEquals(
            1,
            AttendanceSession::where('class_id', $this->class->id)
                ->whereDate('date', now()->toDateString())
                ->count()
        );
    }
}
