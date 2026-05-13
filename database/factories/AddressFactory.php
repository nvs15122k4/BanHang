<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recipient_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'province' => fake()->city(),
            'district' => fake()->citySuffix(),
            'ward' => fake()->streetSuffix(),
            'detail' => fake()->streetAddress(),
            'is_default' => false,
        ];
    }
}
