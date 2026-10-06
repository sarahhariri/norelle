<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category_slug' => 'dresses',
                'name' => 'Sage Satin Dress',
                'slug' => 'sage-satin-dress',
                'description' => 'Elegant satin dress with a soft flowing fit.',
                'price' => 75.00,
                'sale_price' => 65.00,
                'main_image' => 'products/sage-satin-dress.webp',
                'is_featured' => true,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'category_slug' => 'dresses',
                'name' => 'Mauve Wrap Dress',
                'slug' => 'mauve-wrap-dress',
                'description' => 'A graceful satin wrap dress with elegant draping.',
                'price' => 89.00,
                'sale_price' => 74.00,
                'main_image' => 'products/mauve-wrap-dress.webp',
                'is_featured' => true,
                'is_active' => true,
                'order' => 2,
            ],
            [
                'category_slug' => 'sets',
                'name' => 'Dusty Rose Tailored Set',
                'slug' => 'dusty-rose-tailored-set',
                'description' => 'A polished blazer and wide-leg trousers set.',
                'price' => 110.00,
                'sale_price' => 95.00,
                'main_image' => 'products/dusty-rose-tailored-set.webp',
                'is_featured' => true,
                'is_active' => true,
                'order' => 3,
            ],
            [
                'category_slug' => 'tops',
                'name' => 'Ivory Satin Blouse',
                'slug' => 'ivory-satin-blouse',
                'description' => 'A timeless satin blouse with refined gathered details.',
                'price' => 52.00,
                'sale_price' => null,
                'main_image' => 'products/ivory-satin-blouse.webp',
                'is_featured' => false,
                'is_active' => true,
                'order' => 4,
            ],

            [
                'category_slug' => 'outerwear',
                'name' => 'Classic Beige Trench Coat',
                'slug' => 'classic-beige-trench-coat',
                'description' => 'A timeless double-breasted coat, ideal for effortless layering.',
                'price' => 95.00,
                'sale_price' => null,
                'main_image' => 'products/classic-beige-trench-coat.png',
                'is_featured' => false,
                'is_active' => true,
                'order' => 5,
            ],
            [
                'category_slug' => 'skirts',
                'name' => 'Cocoa Pleated Skirt',
                'slug' => 'cocoa-pleated-skirt',
                'description' => 'An elegant pleated skirt for effortless everyday style.',
                'price' => 48.00,
                'sale_price' => null,
                'main_image' => 'products/cocoa-pleated-skirt.png',
                'is_featured' => false,
                'is_active' => true,
                'order' => 6,
            ],
            [
                'category_slug' => 'pants',
                'name' => 'Taupe Wide-Leg Trousers',
                'slug' => 'taupe-wide-leg-trousers',
                'description' => 'Refined wide-leg trousers with a relaxed, polished silhouette.',
                'price' => 52.00,
                'sale_price' => 48.00,
                'main_image' => 'products/taupe-wide-leg-trousers.png',
                'is_featured' => false,
                'is_active' => true,
                'order' => 7,
            ],
            [
                'category_slug' => 'accessories',
                'name' => 'Dusty Rose Handbag',
                'slug' => 'dusty-rose-handbag',
                'description' => 'A chic dusty rose handbag to complete your everyday look.',
                'price' => 45.00,
                'sale_price' => null,
                'main_image' => 'products/dusty-rose-handbag.png',
                'is_featured' => false,
                'is_active' => true,
                'order' => 8,
            ],
        ];

        foreach ($products as $productData) {
            $category = Category::where(
                'slug',
                $productData['category_slug']
            )->firstOrFail();

            unset($productData['category_slug']);

            Product::updateOrCreate(
                ['slug' => $productData['slug']],
                [
                    ...$productData,
                    'category_id' => $category->id,
                ]
            );
        }
    }
}
