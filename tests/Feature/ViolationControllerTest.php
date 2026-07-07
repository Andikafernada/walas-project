<?php

namespace Tests\Feature;

use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Violation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViolationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected ClassModel $class;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->class = ClassModel::factory()->forUser($this->user)->create();
        $this->student = Student::factory()->forClass($this->class)->create([
            'poin' => 100,
        ]);
    }

    public function test_user_can_view_violation_list(): void
    {
        Violation::factory()->count(3)->forStudent($this->student)->create();

        $response = $this->actingAs($this->user)
            ->get(route('classes.violations.index', $this->class));

        $response->assertStatus(200);
        $response->assertSee('Pelanggaran');
    }

    public function test_user_can_add_violation(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('classes.violations.store', $this->class),
            [
                'student_id' => $this->student->id,
                'category' => 'terlambat',
                'description' => 'Terlambat 15 menit',
                'severity' => 'ringan',
                'date' => now()->toDateString(),
            ]
        );

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('violations', [
            'student_id' => $this->student->id,
            'severity' => 'ringan',
        ]);
    }

    public function test_violation_reduces_student_poin(): void
    {
        $initialPoin = $this->student->poin;

        $this->actingAs($this->user)->post(
            route('classes.violations.store', $this->class),
            [
                'student_id' => $this->student->id,
                'category' => 'bolos',
                'description' => 'Bolos 1 hari',
                'severity' => 'sedang',
                'date' => now()->toDateString(),
            ]
        );

        $this->student->refresh();
        $this->assertEquals($initialPoin - 10, $this->student->poin);
    }

    public function test_user_can_delete_violation(): void
    {
        $violation = Violation::factory()->forStudent($this->student)->create([
            'poin_reduced' => 5,
        ]);

        $this->student->decrement('poin', 5);

        $response = $this->actingAs($this->user)
            ->delete(route('classes.violations.destroy', [$this->class, $violation]));

        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('violations', ['id' => $violation->id]);
    }

    public function test_deleting_violation_restores_poin(): void
    {
        $violation = Violation::factory()->forStudent($this->student)->create([
            'poin_reduced' => 5,
            'poin_before' => 100,
            'poin_after' => 95,
        ]);

        $this->student->update(['poin' => 95]);

        $this->actingAs($this->user)
            ->delete(route('classes.violations.destroy', [$this->class, $violation]));

        $this->student->refresh();
        $this->assertEquals(100, $this->student->poin);
    }

    public function test_violation_filter_by_severity(): void
    {
        Violation::factory()->forStudent($this->student)->create(['severity' => 'ringan']);
        Violation::factory()->forStudent($this->student)->create(['severity' => 'berat']);

        $response = $this->actingAs($this->user)
            ->get(route('classes.violations.index', $this->class) . '?severity=berat');

        $response->assertStatus(200);
    }

    public function test_violation_validation_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('classes.violations.store', $this->class),
            []
        );

        $response->assertSessionHasErrors(['student_id', 'category', 'description', 'severity', 'date']);
    }
}
