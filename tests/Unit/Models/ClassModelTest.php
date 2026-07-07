<?php

namespace Tests\Unit\Models;

use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_class(): void
    {
        $user = User::factory()->create();
        $class = ClassModel::factory()->forUser($user)->create();

        $this->assertDatabaseHas('classes', [
            'id' => $class->id,
            'name' => $class->name,
            'user_id' => $user->id,
        ]);
    }

    public function test_class_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $class = ClassModel::factory()->forUser($user)->create();

        $this->assertInstanceOf(User::class, $class->user);
        $this->assertEquals($user->id, $class->user->id);
    }

    public function test_class_has_many_students(): void
    {
        $class = ClassModel::factory()->create();
        $students = Student::factory()->count(5)->forClass($class)->create();

        $this->assertCount(5, $class->students);
        $this->assertInstanceOf(Student::class, $class->students->first());
    }

    public function test_class_student_count_attribute(): void
    {
        $class = ClassModel::factory()->create();
        Student::factory()->count(3)->forClass($class)->create();
        Student::factory()->forClass($class)->create(['is_active' => false]);

        $this->assertEquals(3, $class->student_count);
    }

    public function test_class_full_name_attribute(): void
    {
        $class = ClassModel::factory()->create([
            'name' => 'X IPA',
            'jurusan' => 'IPA',
        ]);

        $this->assertEquals('X IPA - IPA', $class->full_name);
    }

    public function test_class_can_be_inactive(): void
    {
        $class = ClassModel::factory()->create(['is_active' => false]);

        $this->assertFalse($class->is_active);
    }

    public function test_class_has_many_schedules(): void
    {
        $class = ClassModel::factory()->create();
        $class->schedules()->create([
            'subject' => 'Matematika',
            'day' => 'senin',
            'start_time' => '07:00',
            'end_time' => '08:30',
        ]);

        $this->assertCount(1, $class->schedules);
    }

    public function test_class_has_many_violations(): void
    {
        $class = ClassModel::factory()->create();
        $student = Student::factory()->forClass($class)->create();

        $student->violations()->create([
            'user_id' => $class->user_id,
            'category' => 'terlambat',
            'description' => 'Terlambat 15 menit',
            'severity' => 'ringan',
            'date' => now()->toDateString(),
            'poin_reduced' => 5,
            'poin_before' => 100,
            'poin_after' => 95,
        ]);

        $this->assertCount(1, $class->violations);
    }
}
