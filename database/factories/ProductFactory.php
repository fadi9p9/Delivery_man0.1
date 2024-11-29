<?php

namespace Database\Factories;

use App\Models\Market;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
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
            'subcategoryId' => Subcategory::factory(),
            'marketId' => Market::factory(),
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'discount' => $this->faker->numberBetween(0, 100),
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            'totalQuantity' => $this->faker->numberBetween(1, 100),
            'rate' => $this->faker->randomFloat(2, 1, 5),
            'rating_count' => $this->faker->numberBetween(0, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
