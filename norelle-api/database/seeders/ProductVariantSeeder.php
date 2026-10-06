<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $slugs = [
            'sage-satin-dress',
            'mauve-wrap-dress',
            'dusty-rose-tailored-set',
            'ivory-satin-blouse',
            'classic-beige-trench-coat',
            'cocoa-pleated-skirt',
            'taupe-wide-leg-trousers',
            'dusty-rose-handbag',
        ];

        Product::whereIn('slug', $slugs)->each(function (Product $product) {
            foreach (['S', 'M', 'L'] as $size) {
                $product->variants()->updateOrCreate(
                    ['size' => $size],
                    ['stock' => 5]
                );
            }
        });
    }
}
