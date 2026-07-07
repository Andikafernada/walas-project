<?php

namespace Database\Factories;

use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['laki-laki', 'perempuan']);
        $religions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha'];

        return [
            'class_id' => ClassModel::factory(),
            'nisn' => fake()->unique()->numerify('##########'),
            'nis' => fake()->unique()->numerify('######'),
            'name' => fake()->name($gender === 'laki-laki' ? 'male' : 'female'),
            'gender' => $gender,
            'birth_date' => fake()->date('Y-m-d', '-10 years'),
            'birth_place' => fake()->city(),
            'religion' => fake()->randomElement($religions),
            'address' => fake()->address(),
            'father_name' => fake()->name('male'),
            'mother_name' => fake()->name('female'),
            'parent_phone' => fake()->numerify('08##########'),
            'parent_whatsapp' => fake()->numerify('08##########'),
            'emergency_contact' => fake()->numerify('08##########'),
            'photo' => null,
            'poin' => 100,
            'is_active' => true,
        ];
    }

    /**
     * Male student.
     */
    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'laki-laki',
            'name' => fake()->name('male'),
        ]);
    }

    /**
     * Female student.
     */
    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'perempuan',
            'name' => fake()->name('female'),
        ]);
    }

    /**
     * Inactive student.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Student with low poin.
     */
    public function withLowPoin(): static
    {
        return $this->state(fn (array $attributes) => [
            'poin' => fake()->numberBetween(10, 50),
        ]);
    }

    /**
     * Student with warning poin (60-80).
     */
    public function withWarningPoin(): static
    {
        return $this->state(fn (array $attributes) => [
            'poin' => fake()->numberBetween(60, 80),
        ]);
    }

    /**
     * Set specific class.
     */
    public function forClass(ClassModel $class): static
    {
        return $this->state(fn (array $attributes) => [
            'class_id' => $class->id,
        ]);
    }
}
