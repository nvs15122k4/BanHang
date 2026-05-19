<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class UpdateProductSizesSeeder extends Seeder
{
    public function run(): void
    {
        $defaultSizes = ['S', 'M', 'L', 'XL'];

        Product::whereNull('sizes')->update([
            'sizes' => json_encode($defaultSizes),
        ]);
    }
}
