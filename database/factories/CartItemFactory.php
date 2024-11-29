<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItem>
 */
class CartItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'id' => $this->faker->unique()->randomNumber(),
            'cartId' => Cart::factory(), // ربط العنصر بعربة تسوق وهمية
            'productId' => Product::factory(), // ربط العنصر بمنتج وهمي
            'quantity' => $this->faker->numberBetween(1, 10), // كمية بين 1 و10
            // 'price' => $this->faker->randomFloat(2, 1, 100), // سعر بين 1 و100 (دقة 2)
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
