<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('Password123'),
            'role' => 'user',
            'phone' => $this->faker->phoneNumber(),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            // User model casts birthday to date, so store as Y-m-d string
            'birthday' => $this->faker->date('Y-m-d'),
            'avatar' => User::DEFAULT_AVATAR_URL,
            'is_active' => true,
            'remember_token' => Str::random(10),
            // Support email verification tests
            'email_verified_at' => now(),
        ];

    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => 'admin']);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}

