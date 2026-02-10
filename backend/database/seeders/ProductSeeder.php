<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Classic Cotton Tee',
                'description' => 'Soft cotton tee with a relaxed fit.',
                'price' => 24.99,
                'stock' => 120,
                'category' => 'Tops',
                'image_url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab',
            ],
            [
                'name' => 'Everyday Hoodie',
                'description' => 'Midweight hoodie for all seasons.',
                'price' => 54.00,
                'stock' => 80,
                'category' => 'Outerwear',
                'image_url' => 'https://images.unsplash.com/photo-1503341455253-b2e723bb3dbb',
            ],
            [
                'name' => 'Slim Denim Jeans',
                'description' => 'Stretch denim with a modern slim fit.',
                'price' => 69.50,
                'stock' => 60,
                'category' => 'Bottoms',
                'image_url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab',
            ],
            [
                'name' => 'Linen Button Down',
                'description' => 'Breathable linen shirt for warm days.',
                'price' => 49.00,
                'stock' => 45,
                'category' => 'Tops',
                'image_url' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1',
            ],
            [
                'name' => 'Utility Jacket',
                'description' => 'Lightweight jacket with multiple pockets.',
                'price' => 89.00,
                'stock' => 35,
                'category' => 'Outerwear',
                'image_url' => 'https://images.unsplash.com/photo-1503341455253-b2e723bb3dbb',
            ],
            [
                'name' => 'Everyday Chino',
                'description' => 'Comfortable chino pants for daily wear.',
                'price' => 58.00,
                'stock' => 70,
                'category' => 'Bottoms',
                'image_url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab',
            ],
        ];

        $sizes = [
            ['size' => 'S', 'stock' => 20],
            ['size' => 'M', 'stock' => 30],
            ['size' => 'L', 'stock' => 25],
            ['size' => 'XL', 'stock' => 15],
        ];

        foreach ($products as $product) {
            $created = Product::create($product);
            $created->sizes()->createMany($sizes);
        }
    }
}
