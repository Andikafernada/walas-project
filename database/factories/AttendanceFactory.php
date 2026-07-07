<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'attendance_session_id' => AttendanceSession::factory(),
            'student_id' => Student::factory(),
            'user_id' => User::factory(),
            'class_id' => ClassModel::factory(),
            'date' => fake()->date(),
            'status' => fake()->randomElement(['hadir', 'terlambat', 'sakit', 'izin', 'alpa']),
            'notes' => fake()->sentence(),
            'minutes_late' => null,
            'attachment' => null,
        ];
    }

    /**
     * Hadir status.
     */
    public function hadir(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'hadir',
        ]);
    }

    /**
     * Terlambat status.
     */
    public function terlambat(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'terlambat',
            'minutes_late' => fake()->numberBetween(5, 60),
        ]);
    }

    /**
     * Sakit status.
     */
    public function sakit(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sakit',
        ]);
    }

    /**
     * Izin status.
     */
    public function izin(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'izin',
        ]);
    }

    /**
     * Alpa status.
     */
    public function alpa(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'alpa',
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
