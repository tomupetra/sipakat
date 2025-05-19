<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'user',      // default role
            'id_tugas' => 1        // default sebagai pemusik
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'admin',
            'name' => 'Admin',
        ]);
    }

    public function pemusik(): static
    {
        return $this->state(fn(array $attributes) => [
            'id_tugas' => 1,
            'role' => 'user',
        ]);
    }

    public function songLeader(): static
    {
        return $this->state(fn(array $attributes) => [
            'id_tugas' => 2,
            'role' => 'user',
        ]);
    }
}
