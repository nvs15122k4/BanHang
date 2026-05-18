<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            // user_id sẽ được test override
            'user_id' => null,
            'recipient_name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'province' => 'Ho Chi Minh',
            'district' => 'District 1',
            'ward' => 'Ward 1',
            'detail' => $this->faker->streetAddress(),
            'is_default' => false,
        ];
    }

    public function default(): static
    {
        return $this->state(fn () => ['is_default' => true]);
    }
}

