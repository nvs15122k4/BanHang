<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ten_sp' => $this->faker->word(5, true),
            'mo_ta' => $this->faker->paragraph(3),
            'gia' => $this->faker->numberBetween(50000, 50000000),
            'so_luong' => $this->faker->numberBetween(0, 100),
            'trang_thai' => $this->faker->randomElement(['con', 'het']),
            'anh' => null, // Sẽ sử dụng ảnh mặc định
        ];
    }
}