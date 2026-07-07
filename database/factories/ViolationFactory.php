<?php

namespace Database\Factories;

use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Violation>
 */
class ViolationFactory extends Factory
{
    protected $model = Violation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $severity = fake()->randomElement(['ringan', 'sedang', 'berat']);
        $poinMap = ['ringan' => 5, 'sedang' => 10, 'berat' => 15];
        $poinReduced = $poinMap[$severity];

        return [
            'student_id' => Student::factory(),
            'user_id' => User::factory(),
            'class_id' => ClassModel::factory(),
            'category' => fake()->randomElement([
                'terlambat',
                'tidak_mengerjakan_tugas',
                'mengganggu_teman',
                'bolos',
                'hp_di_kelas',
                'lainnya',
            ]),
            'description' => fake()->sentence(),
            'poin_reduced' => $poinReduced,
            'poin_before' => 100,
            'poin_after' => 100 - $poinReduced,
            'severity' => $severity,
            'date' => fake()->date(),
            'attachment' => null,
            'status' => 'approved',
            'admin_notes' => null,
        ];
    }

    /**
     * Ringan severity.
     */
    public function ringan(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => 'ringan',
            'poin_reduced' => 5,
        ]);
    }

    /**
     * Sedang severity.
     */
    public function sedang(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => 'sedang',
            'poin_reduced' => 10,
        ]);
    }

    /**
     * Berat severity.
     */
    public function berat(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => 'berat',
            'poin_reduced' => 15,
        ]);
    }

    /**
     * For specific student.
     */
    public function forStudent(Student $student): static
    {
        return $this->state(fn (array $attributes) => [
            'student_id' => $student->id,
            'class_id' => $student->class_id,
        ]);
    }
}
