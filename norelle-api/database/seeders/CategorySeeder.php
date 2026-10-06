<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Dresses',
                'slug' => 'dresses',
                'image' => 'categories/dresses.jpeg',
                'description' => 'Elegant dresses for every occasion.',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'Sets',
                'slug' => 'sets',
                'image' => 'categories/sets.jpeg',
                'description' => 'Refined matching sets for effortless style.',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'Tops',
                'slug' => 'tops',
                'image' => 'categories/tops.jpeg',
                'description' => 'Timeless tops designed for elegant looks.',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'name' => 'Skirts',
                'slug' => 'skirts',
                'image' => 'categories/skirts.jpeg',
                'description' => 'Elegant skirts for effortless style.',
                'is_active' => true,
                   'order' => 4,
                   ],
            [
                'name' => 'Pants',
                'slug' => 'pants',
                'image' => 'categories/pants.jpeg',
                'description' => 'Sophisticated pants for a polished look.',
                'is_active' => true,
                'order' => 5,
            ],
            [
                'name' => 'Outerwear',
                'slug' => 'outerwear',
                'image' => 'categories/outerwear.jpeg',
                'description' => 'Chic outerwear for stylish layering.',
                'is_active' => true,
                'order' => 6,
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'image' => 'categories/accessories.jpeg',
                'description' => 'Refined accessories to complete your look.',              
                'is_active' => true,
                'order' => 7,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}