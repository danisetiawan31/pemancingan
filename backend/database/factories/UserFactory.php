<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->unique()->numerify('08##########'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'address' => fake()->address(),
            'role' => 'member',
            'status' => 'active',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function owner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'owner',
            'status' => 'active',
        ]);
    }

    public function employee(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'employee',
            'status' => 'active',
        ]);
    }

    public function pendingMember(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'member',
            'status' => 'pending',
        ]);
    }

    public function rejectedMember(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'member',
            'status' => 'rejected',
            'rejection_reason' => 'Data tidak valid',
            'rejected_at' => now(),
        ]);
    }

    public function deactivatedMember(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'member',
            'status' => 'deactivated',
            'deactivated_reason' => 'Melanggar aturan',
            'deactivated_at' => now(),
        ]);
    }
}
