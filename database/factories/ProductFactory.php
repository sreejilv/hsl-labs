<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $purchasePrice = $this->faker->randomFloat(2, 10, 500);
        
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'purchase_price' => $purchasePrice,
            'selling_price' => $purchasePrice * 1.2, // 20% markup
            'stock' => $this->faker->numberBetween(0, 100),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}