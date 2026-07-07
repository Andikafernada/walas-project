<?php

namespace Tests\Feature;

use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentControllerTest extends TestCase
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

    public function test_user_can_view_student_list(): void
    {
        Student::factory()->count(5)->forClass($this->class)->create();

        $response = $this->actingAs($this->user)
            ->get(route('classes.students.index', $this->class));

        $response->assertStatus(200);
        $response->assertSee('Siswa');
    }

    public function test_user_can_add_student(): void
    {
        $response = $this->actingAs($this->user)->post(route('classes.students.store', $this->class), [
            'name' => 'John Doe',
            'gender' => 'laki-laki',
            'nisn' => '1234567890',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('students', [
            'name' => 'John Doe',
            'class_id' => $this->class->id,
        ]);
    }

    public function test_user_can_update_student(): void
    {
        $student = Student::factory()->forClass($this->class)->create([
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($this->user)->put(
            route('classes.students.update', [$this->class, $student]),
            ['name' => 'New Name']
        );

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'New Name',
        ]);
    }

    public function test_user_can_deactivate_student(): void
    {
        $student = Student::factory()->forClass($this->class)->create();

        $response = $this->actingAs($this->user)
            ->delete(route('classes.students.destroy', [$this->class, $student]));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'is_active' => false,
        ]);
    }

    public function test_user_cannot_access_other_users_class_students(): void
    {
        $otherClass = ClassModel::factory()->create();
        $student = Student::factory()->forClass($otherClass)->create();

        $response = $this->actingAs($this->user)
            ->get(route('classes.students.index', $otherClass));

        $response->assertStatus(403);
    }

    public function test_student_validation_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('classes.students.store', $this->class),
            []
        );

        $response->assertSessionHasErrors(['name']);
    }

    public function test_student_search_functionality(): void
    {
        Student::factory()->forClass($this->class)->create(['name' => 'John Doe']);
        Student::factory()->forClass($this->class)->create(['name' => 'Jane Smith']);

        $response = $this->actingAs($this->user)
            ->get(route('classes.students.index', $this->class) . '?search=John');

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Smith');
    }
}
