<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductQuantitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing products with random stock quantities
        $products = Product::all();
        
        foreach ($products as $product) {
            $product->update([
                'stock' => rand(5, 100) // Random stock between 5 and 100
            ]);
        }
        
        $this->command->info('Updated ' . $products->count() . ' products with inventory stock quantities.');
    }
}
