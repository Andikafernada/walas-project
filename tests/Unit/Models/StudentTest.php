<?php

namespace Tests\Unit\Models;

use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Violation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_student(): void
    {
        $class = ClassModel::factory()->create();
        $student = Student::factory()->forClass($class)->create([
            'name' => 'John Doe',
        ]);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'John Doe',
            'class_id' => $class->id,
        ]);
    }

    public function test_student_belongs_to_class(): void
    {
        $class = ClassModel::factory()->create();
        $student = Student::factory()->forClass($class)->create();

        $this->assertInstanceOf(ClassModel::class, $student->classModel);
        $this->assertEquals($class->id, $student->classModel->id);
    }

    public function test_student_user_attribute(): void
    {
        $class = ClassModel::factory()->create();
        $student = Student::factory()->forClass($class)->create();

        $this->assertInstanceOf(User::class, $student->user);
        $this->assertEquals($class->user_id, $student->user->id);
    }

    public function test_student_has_many_attendances(): void
    {
        $student = Student::factory()->create();
        $student->attendances()->create([
            'user_id' => $student->classModel->user_id,
            'class_id' => $student->class_id,
            'date' => now()->toDateString(),
            'status' => 'hadir',
        ]);

        $this->assertCount(1, $student->attendances);
    }

    public function test_student_has_many_violations(): void
    {
        $student = Student::factory()->create();

        Violation::factory()->forStudent($student)->create();

        $this->assertCount(1, $student->violations);
    }

    public function test_student_parent_contact_attribute(): void
    {
        $studentWithWhatsApp = Student::factory()->create([
            'parent_whatsapp' => '628123456789',
            'parent_phone' => '021234567',
        ]);

        $studentWithoutWhatsApp = Student::factory()->create([
            'parent_whatsapp' => null,
            'parent_phone' => '021234567',
        ]);

        $this->assertEquals('628123456789', $studentWithWhatsApp->parent_contact);
        $this->assertEquals('021234567', $studentWithoutWhatsApp->parent_contact);
    }

    public function test_student_formatted_poin_attribute(): void
    {
        $studentHigh = Student::factory()->create(['poin' => 85]);
        $studentMedium = Student::factory()->create(['poin' => 70]);
        $studentLow = Student::factory()->create(['poin' => 50]);

        $this->assertStringContainsString('green', $studentHigh->formatted_poin);
        $this->assertStringContainsString('yellow', $studentMedium->formatted_poin);
        $this->assertStringContainsString('red', $studentLow->formatted_poin);
    }

    public function test_student_can_be_inactive(): void
    {
        $student = Student::factory()->create(['is_active' => false]);

        $this->assertFalse($student->is_active);
    }

    public function test_student_soft_deletes(): void
    {
        $student = Student::factory()->create();
        $studentId = $student->id;

        $student->delete();

        $this->assertSoftDeleted('students', ['id' => $studentId]);
    }
}
