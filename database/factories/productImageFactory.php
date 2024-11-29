<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\productImage>
 */
class productImageFactory extends Factory
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
            // 'productId' => Product::factory(), // اختار id عشوائي موجود من جدول products
            'productId' => Product::inRandomOrder()->value('id'),
            'url' => $this->faker->imageUrl(),
            'created_at' => now(),
            'updated_at' => now(),

        ];
    }
}
