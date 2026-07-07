<?php

namespace Database\Factories;

use App\Models\AttendanceSession;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AttendanceSession>
 */
class AttendanceSessionFactory extends Factory
{
    protected $model = AttendanceSession::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'class_id' => ClassModel::factory(),
            'user_id' => User::factory(),
            'date' => fake()->date(),
            'token' => Str::random(64),
            'pin' => str_pad(fake()->numberBetween(0, 9999), 4, '0', STR_PAD_LEFT),
            'method' => 'magic_link',
            'status' => fake()->randomElement(['active', 'used', 'expired']),
            'expires_at' => now()->addHours(8),
            'submitted_at' => null,
            'submitted_by' => null,
            'submitted_by_name' => null,
        ];
    }

    /**
     * Active session.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'expires_at' => now()->addHours(8),
        ]);
    }

    /**
     * Used session.
     */
    public function used(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'used',
            'submitted_at' => now(),
        ]);
    }

    /**
     * Expired session.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'expires_at' => now()->subHours(1),
        ]);
    }

    /**
     * Today's session.
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => now()->toDateString(),
        ]);
    }

    /**
     * For specific class.
     */
    public function forClass(ClassModel $class): static
    {
        return $this->state(fn (array $attributes) => [
            'class_id' => $class->id,
        ]);
    }
}
