<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Categories
        $categories = [
            [
                'name' => 'Main Dishes',
                'slug' => 'main-dishes',
                'description' => 'Full meals and main course items',
                'color' => '#8b5cf6'
            ],
            [
                'name' => 'Snacks',
                'slug' => 'snacks',
                'description' => 'Light snacks and quick bites',
                'color' => '#10b981'
            ],
            [
                'name' => 'Breakfast',
                'slug' => 'breakfast',
                'description' => 'Morning breakfast items',
                'color' => '#f59e0b'
            ],
            [
                'name' => 'Plain Dishes',
                'slug' => 'plain-dishes',
                'description' => 'Simple and plain food items',
                'color' => '#3b82f6'
            ]
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Get created categories
        $mainDishes = Category::where('slug', 'main-dishes')->first();
        $snacks = Category::where('slug', 'snacks')->first();
        $breakfast = Category::where('slug', 'breakfast')->first();
        $plainDishes = Category::where('slug', 'plain-dishes')->first();

        // Create Products based on screenshot
        $products = [
            // Main Dishes
            ['name' => 'Staff Meal', 'category_id' => $mainDishes->id, 'price' => 50.00, 'stock_quantity' => 100],
            
            // Snacks
            ['name' => 'Sausage', 'category_id' => $snacks->id, 'price' => 50.00, 'stock_quantity' => 50],
            ['name' => 'Andazi Small', 'category_id' => $snacks->id, 'price' => 10.00, 'stock_quantity' => 200],
            ['name' => 'Beef Smoke', 'category_id' => $snacks->id, 'price' => 30.00, 'stock_quantity' => 30],
            ['name' => 'Chapati Staff', 'category_id' => $snacks->id, 'price' => 20.00, 'stock_quantity' => 80],
            ['name' => 'Vanilla Plain Cake', 'category_id' => $snacks->id, 'price' => 70.00, 'stock_quantity' => 20],
            ['name' => 'Mandazi', 'category_id' => $snacks->id, 'price' => 20.00, 'stock_quantity' => 150],
            ['name' => 'Egg Boiled', 'category_id' => $snacks->id, 'price' => 30.00, 'stock_quantity' => 40],
            ['name' => 'Sweet Bread Large', 'category_id' => $snacks->id, 'price' => 80.00, 'stock_quantity' => 25],
            ['name' => 'Samosa', 'category_id' => $snacks->id, 'price' => 50.00, 'stock_quantity' => 60],
            ['name' => 'Rock Bun', 'category_id' => $snacks->id, 'price' => 25.00, 'stock_quantity' => 45],
            
            // Breakfast
            ['name' => 'Tea Mug', 'category_id' => $breakfast->id, 'price' => 50.00, 'stock_quantity' => 200],
            ['name' => 'Nduma', 'category_id' => $breakfast->id, 'price' => 40.00, 'stock_quantity' => 30],
            
            // Plain Dishes
            ['name' => 'Vegetables Plain', 'category_id' => $plainDishes->id, 'price' => 50.00, 'stock_quantity' => 80],
            ['name' => 'Pilau Plain', 'category_id' => $plainDishes->id, 'price' => 250.00, 'stock_quantity' => 20],
            ['name' => 'Ngwaci', 'category_id' => $plainDishes->id, 'price' => 35.00, 'stock_quantity' => 40],
        ];

        foreach ($products as $productData) {
            $productData['slug'] = \Str::slug($productData['name']);
            Product::create($productData);
        }

        echo "Created " . Category::count() . " categories and " . Product::count() . " products\n";
    }
}
