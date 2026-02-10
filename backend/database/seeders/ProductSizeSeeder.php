<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSizeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = [
            ['size' => 'S', 'stock' => 20],
            ['size' => 'M', 'stock' => 30],
            ['size' => 'L', 'stock' => 25],
            ['size' => 'XL', 'stock' => 15],
        ];

        Product::query()->each(function (Product $product) use ($sizes) {
            if ($product->sizes()->count() === 0) {
                $product->sizes()->createMany($sizes);
            }
        });
    }
}
