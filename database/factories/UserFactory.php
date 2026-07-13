<?php

namespace Database\Factories;

use App\Enums\RoleUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Factory User — membuat data dummy akun Owner dan Admin.
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'role'              => RoleUser::Admin,
            'remember_token'    => Str::random(10),
        ];
    }

    /** State: jadikan user sebagai Owner. */
    public function owner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => RoleUser::Owner,
        ]);
    }

    /** State: jadikan user sebagai Admin. */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => RoleUser::Admin,
        ]);
    }

    /** State: email belum diverifikasi. */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
