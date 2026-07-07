<?php

namespace Database\Factories;

use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassModel>
 */
class ClassModelFactory extends Factory
{
    protected $model = ClassModel::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $tingkat = fake()->randomElement(['X', 'XI', 'XII', 'XIII']);
        $jurusan = fake()->randomElement(['IPA', 'IPS', 'Bahasa', 'Umum']);

        return [
            'user_id' => User::factory(),
            'name' => "{$tingkat} {$jurusan} " . fake()->numberBetween(1, 5),
            'alias' => strtoupper(substr($tingkat, 0, 1)) . substr($jurusan, 0, 2) . fake()->numberBetween(1, 5),
            'jurusan' => $jurusan,
            'tingkat' => $tingkat,
            'school_year_start' => fake()->numberBetween(2020, 2024),
            'school_year_end' => fake()->numberBetween(2021, 2025),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * Create IPA class.
     */
    public function ipa(): static
    {
        return $this->state(fn (array $attributes) => [
            'jurusan' => 'IPA',
            'name' => 'X IPA 1',
        ]);
    }

    /**
     * Create IPS class.
     */
    public function ips(): static
    {
        return $this->state(fn (array $attributes) => [
            'jurusan' => 'IPS',
            'name' => 'X IPS 1',
        ]);
    }

    /**
     * Set specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
